<?php

class CategoriesHandler implements HandlerInterface
{
    public function prepare($data) {
        $categories = array();

        foreach ($data as $record) {
            $category = array(
                'id' => $record['id'],
                'name' => $record['name']
            );
            
            if (!empty($record['parent_id'])) {
                $category['parentId'] = $record['parent_id'];
            }
            
            $categories[] = $category;
        }

        return $categories;
    }
}
