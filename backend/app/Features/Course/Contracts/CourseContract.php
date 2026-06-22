<?php
namespace App\Features\Course\Contracts;

interface CourseContract
{
    public function all();
    public function findById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}
?>
