<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\CategoryRepository;
use PDOException;

class CategoryController extends Controller
{
    private const DEMO_USER_ID = 1;

    private CategoryRepository $categories;

    public function __construct()
    {
        $this->categories = new CategoryRepository();
    }

    public function index(): string
    {
        return $this->view('categories/index', [
            'title' => 'Categories',
            'categories' => $this->categories->allByUser(self::DEMO_USER_ID),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function create(): string
    {
        return $this->view('categories/create', [
            'title' => 'Create Category',
            'category' => $this->defaultCategory(),
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->categoryDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('categories/create', [
                'title' => 'Create Category',
                'category' => $data,
                'errors' => $errors,
            ]);
        }

        try {
            $this->categories->create(self::DEMO_USER_ID, $data);
            $this->flash('success', \__('flash.category_created'));

            return $this->redirect('/categories');
        } catch (PDOException) {
            http_response_code(422);

            return $this->view('categories/create', [
                'title' => 'Create Category',
                'category' => $data,
                'errors' => ['name' => \__('validation.category_duplicate')],
            ]);
        }
    }

    public function edit(string $id): string
    {
        $category = $this->findCategoryOrFail((int) $id);

        return $this->view('categories/edit', [
            'title' => 'Edit Category',
            'category' => $category,
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $category = $this->findCategoryOrFail((int) $id);
        $data = $this->categoryDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('categories/edit', [
                'title' => 'Edit Category',
                'category' => array_merge($category, $data),
                'errors' => $errors,
            ]);
        }

        try {
            $this->categories->update((int) $id, self::DEMO_USER_ID, $data);
            $this->flash('success', \__('flash.category_updated'));

            return $this->redirect('/categories');
        } catch (PDOException) {
            http_response_code(422);

            return $this->view('categories/edit', [
                'title' => 'Edit Category',
                'category' => array_merge($category, $data),
                'errors' => ['name' => \__('validation.category_duplicate')],
            ]);
        }
    }

    public function delete(string $id): string
    {
        $category = $this->findCategoryOrFail((int) $id);

        return $this->view('categories/delete', [
            'title' => 'Delete Category',
            'category' => $category,
            'errors' => [],
        ]);
    }

    public function destroy(string $id): string
    {
        $category = $this->findCategoryOrFail((int) $id);

        if ((int) $category['activities_count'] > 0) {
            http_response_code(422);

            return $this->view('categories/delete', [
                'title' => 'Delete Category',
                'category' => $category,
                'errors' => ['category' => \__('validation.category_delete_blocked')],
            ]);
        }

        $this->categories->delete((int) $id, self::DEMO_USER_ID);
        $this->flash('success', \__('flash.category_deleted'));

        return $this->redirect('/categories');
    }

    private function categoryDataFromRequest(): array
    {
        return [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'color' => trim((string) ($_POST['color'] ?? '#2563eb')),
            'sort_order' => max(0, (int) ($_POST['sort_order'] ?? 0)),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = \__('validation.category_name_required');
        }

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $data['color'])) {
            $errors['color'] = \__('validation.color_hex');
        }

        return $errors;
    }

    private function defaultCategory(): array
    {
        return [
            'name' => '',
            'color' => '#2563eb',
            'sort_order' => 0,
        ];
    }

    private function findCategoryOrFail(int $id): array
    {
        $category = $this->categories->findByUser($id, self::DEMO_USER_ID);

        if ($category !== null) {
            return $category;
        }

        http_response_code(404);
        exit(\__('not_found.category'));
    }
}
