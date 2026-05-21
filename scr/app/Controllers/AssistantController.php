<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SmartAssistantService;

class AssistantController extends Controller
{
    private const DEMO_USER_ID = 1;

    private SmartAssistantService $assistant;

    public function __construct()
    {
        $this->assistant = new SmartAssistantService();
    }

    public function index(): string
    {
        return $this->view('assistant/index', [
            'title' => __('nav.assistant'),
            'suggestions' => $this->assistant->generate($this->authUserId()),
        ]);
    }

    public function generate(): string
    {
        return $this->index();
    }
}
