<?php
function getCategoriesForSelect($db)
{
    $categoriesQuery = $db->prepare(
        "SELECT id, name FROM categories"
    );
    $categoriesQuery->execute([]);

    return $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);
}
