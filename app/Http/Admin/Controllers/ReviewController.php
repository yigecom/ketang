<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Review as ReviewService;

/**
 * @RoutePrefix("/admin/review")
 */
class ReviewController extends Controller
{

    /**
     * @Get("/search", name="admin.review.search")
     */
    public function searchAction()
    {
        $reviewService = new ReviewService();

        $publishTypes = $reviewService->getPublishTypes();

        $this->view->setVar('publish_types', $publishTypes);
    }

    /**
     * @Get("/list", name="admin.review.list")
     */
    public function listAction()
    {
        $reviewService = new ReviewService();

        $pager = $reviewService->getReviews();

        $courseId = $this->request->getQuery('course_id', 'int', 0);

        $course = null;

        if ($courseId > 0) {
            $course = $reviewService->getCourse($courseId);
        }

        $this->view->setVar('pager', $pager);
        $this->view->setVar('course', $course);
    }

    /**
     * @Get("/{id:[0-9]+}/edit", name="admin.review.edit")
     */
    public function editAction($id)
    {
        $reviewService = new ReviewService();

        $review = $reviewService->getReview($id);

        $this->view->setVar('review', $review);
    }

    /**
     * @Post("/{id:[0-9]+}/update", name="admin.review.update")
     */
    public function updateAction($id)
    {
        $reviewService = new ReviewService();

        $reviewService->updateReview($id);

        $content = [
            'msg' => '更新评价成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/delete", name="admin.review.delete")
     */
    public function deleteAction($id)
    {
        $reviewService = new ReviewService();

        $reviewService->deleteReview($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '删除评价成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Post("/{id:[0-9]+}/restore", name="admin.review.restore")
     */
    public function restoreAction($id)
    {
        $reviewService = new ReviewService();

        $reviewService->restoreReview($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '还原评价成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Route("/{id:[0-9]+}/moderate", name="admin.review.moderate")
     */
    public function moderateAction($id)
    {
        $reviewService = new ReviewService();

        if ($this->request->isPost()) {

            $reviewService->moderate($id);

            $location = $this->url->get(['for' => 'admin.mod.reviews']);

            $content = [
                'location' => $location,
                'msg' => '审核评价成功',
            ];

            return $this->jsonSuccess($content);
        }

        $review = $reviewService->getReviewInfo($id);

        $this->view->setVar('review', $review);
    }

}
