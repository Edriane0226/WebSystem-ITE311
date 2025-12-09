<?php
namespace App\Models;

use CodeIgniter\Model;
class MaterialModel extends Model {
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'materialCategoryID', 'file_name', 'file_path', 'uploaded_at'];

    public function insertMaterial($data) {
        return $this->insert($data);
    }

    public function getMaterialsByCourse($course_id) {
        $materials = $this->select('materials.*, materialCategories.categoryName')
            ->join('materialCategories', 'materialCategories.categoryID = materials.materialCategoryID', 'left')
            ->where('course_id', $course_id)
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();

        foreach ($materials as &$material) {
            $material['display_name'] = $material['file_name'] !== null && $material['file_name'] !== ''
                ? $material['file_name']
                : basename((string) $material['file_path']);
        }
        unset($material);

        return $materials;
    }

    public function getMaterialsByCourseGrouped($courseId)
    {
        $grouped = [
            'modules' => [],
            'assignments' => [],
            'others' => [],
        ];

        foreach ($this->getMaterialsByCourse($courseId) as $material) {
            $categoryId = isset($material['materialCategoryID']) ? (int) $material['materialCategoryID'] : 0;

            switch ($categoryId) {
                case 1:
                    $grouped['modules'][] = $material;
                    break;
                case 2:
                    $grouped['assignments'][] = $material;
                    break;
                default:
                    $grouped['others'][] = $material;
                    break;
            }
        }

        return $grouped;
    }

}