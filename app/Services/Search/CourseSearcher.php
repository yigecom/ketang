<?php

namespace App\Services\Search;

use Phalcon\Mvc\User\Component;

class CourseSearcher extends Component
{

    /**
     * @var \XS
     */
    protected $xs;

    public function __construct()
    {
        $filename = config_path('xs.course.ini');

        $this->xs = new \XS($filename);
    }

    /**
     * 获取XS
     *
     * @return \XS
     */
    public function getXS()
    {
        return $this->xs;
    }

    /**
     * 获取高亮字段
     *
     * @return array
     */
    public function getHighlightFields()
    {
        return ['title', 'summary'];
    }

    /**
     * 搜索课程
     *
     * @param string $query
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \XSException
     */
    public function search($query, $limit = 15, $offset = 0)
    {
        $search = $this->xs->getSearch();

        $docs = $search->setQuery($query)->setLimit($limit, $offset)->search();

        $total = $search->getLastCount();

        $fields = array_keys($this->xs->getAllFields());

        $items = [];

        foreach ($docs as $doc) {
            $item = [];
            foreach ($fields as $field) {
                if (in_array($field, $this->getHighlightFields())) {
                    $item[$field] = $search->highlight($doc->{$field});
                } else {
                    $item[$field] = $doc->{$field};
                }
            }
            $items[] = $item;
        }

        return [
            'total' => $total,
            'items' => $items,
        ];
    }

    /**
     * 获取相关搜索
     *
     * @param string $query
     * @param int $limit
     * @return array
     * @throws \XSException
     */
    public function getRelatedQuery($query, $limit = 10)
    {
        $search = $this->xs->getSearch();

        $search->setQuery($query);

        return $search->getRelatedQuery($query, $limit);
    }

    /**
     * @param int $limit
     * @param string $type [total => 总量, lastnum => 上周, currnum => 本周]
     * @return array
     * @throws \XSException
     */
    public function getHotQuery($limit = 10, $type = 'total')
    {
        $search = $this->xs->getSearch();

        $hotQuery = $search->getHotQuery($limit, $type);

        return array_keys($hotQuery);
    }

}
