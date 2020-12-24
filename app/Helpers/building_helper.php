<?php

function buildTree(array $elements, $parentId = 0) {

    $branch = array();

    foreach ($elements as &$element) {

        if ($element['idParent'] == $parentId) {
            $children = buildTree($elements, $element['idChild']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[$element['idChild']] = $element;
            unset($element);
        }
    }
    return $branch;
}