<?php

namespace App\Console\Tasks;

use App\Library\Cache\Backend\Redis as RedisCache;
use App\Repos\Course as CourseRepo;
use App\Services\Search\CourseDocument;
use App\Services\Search\CourseSearcher;
use App\Services\Syncer\CourseIndex as CourseIndexSyncer;

class SyncCourseIndexTask extends Task
{

    /**
     * @var RedisCache
     */
    protected $cache;

    /**
     * @var \Redis
     */
    protected $redis;

    public function mainAction()
    {
        $this->cache = $this->getDI()->get('cache');

        $this->redis = $this->cache->getRedis();

        $this->rebuild();
    }

    protected function rebuild()
    {
        $key = $this->getSyncKey();

        $courseIds = $this->redis->sRandMember($key, 100);

        if (!$courseIds) return;

        $courseRepo = new CourseRepo();

        $courses = $courseRepo->findByIds($courseIds);

        if ($courses->count() == 0) {
            return;
        }

        $document = new CourseDocument();

        $handler = new CourseSearcher();

        $index = $handler->getXS()->getIndex();

        $index->openBuffer();

        foreach ($courses as $course) {

            $doc = $document->setDocument($course);

            if ($course->published == 1) {
                $index->update($doc);
            } else {
                $index->del($course->id);
            }
        }

        $index->closeBuffer();

        $this->redis->sRem($key, ...$courseIds);
    }

    protected function getSyncKey()
    {
        $syncer = new CourseIndexSyncer();

        return $syncer->getSyncKey();
    }

}
