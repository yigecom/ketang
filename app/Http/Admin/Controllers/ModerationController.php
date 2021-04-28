<?php

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Moderation as ModerationService;

/**
 * @RoutePrefix("/admin/moderation")
 */
class ModerationController extends Controller
{

    /**
     * @Get("/articles", name="admin.mod.articles")
     */
    public function articlesAction()
    {
        $modService = new ModerationService();

        $pager = $modService->getArticles();

        $this->view->setVar('pager', $pager);
    }

}