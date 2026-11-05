<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Cache;
use App\Core\ImageProcessor;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Project;
use App\Services\Media;

final class ProjectController extends AdminController
{
    public function index(): void
    {
        $filters = [
            'q' => Request::line('q', 120),
            'status' => Request::line('status', 20),
            'category' => Request::line('category', 12),
        ];
        if (!in_array($filters['status'], ['', 'draft', 'published'], true)) {
            $filters['status'] = '';
        }
        $this->admin('admin/projects/index', [
            'pageTitle' => 'Projects',
            'projects' => Project::adminList($filters),
            'categories' => Category::allRows(),
            'filters' => $filters,
        ]);
    }

    public function createForm(): void
    {
        $this->admin('admin/projects/form', [
            'pageTitle' => 'Add project',
            'project' => null,
            'images' => [],
            'categories' => Category::allRows(),
        ]);
    }

    public function store(): void
    {
        $this->guardPost();
        $data = $this->payload(null);
        if ($data['errors'] !== []) {
            $this->backWithErrors('/admin/projects/new', $data['errors'], $data['input']);
        }
        $id = Project::create($data['row']);
        $this->saved('Project created. Add pictures next.', '/admin/projects/' . $id . '/edit');
    }

    public function editForm(string $id): void
    {
        $project = $this->find($id);
        $this->admin('admin/projects/form', [
            'pageTitle' => 'Edit project',
            'project' => $project,
            'images' => Project::images((int) $project['id']),
            'categories' => Category::allRows(),
        ]);
    }

    public function update(string $id): void
    {
        $this->guardPost();
        $project = $this->find($id);
        $data = $this->payload((int) $project['id']);
        if ($data['errors'] !== []) {
            $this->backWithErrors('/admin/projects/' . $project['id'] . '/edit', $data['errors'], $data['input']);
        }
        Project::update((int) $project['id'], $data['row']);
        $this->acceptUploads((int) $project['id'], (string) $project['name']);
        $this->saved('Project saved.', '/admin/projects/' . $project['id'] . '/edit');
    }

    public function destroy(string $id): void
    {
        $this->guardPost();
        $project = $this->find($id);
        $this->removeFiles((int) $project['id']);
        Project::delete((int) $project['id']);
        $this->saved('Project deleted.', '/admin/projects');
    }

    public function bulkDelete(): void
    {
        $this->guardPost();
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids)) {
            $ids = [];
        }
        foreach ($ids as $id) {
            $project = Project::find((int) $id);
            if ($project === null) {
                continue;
            }
            $this->removeFiles((int) $project['id']);
            Project::delete((int) $project['id']);
        }
        $this->saved('Selected projects deleted.', '/admin/projects');
    }

    public function reorder(): void
    {
        $this->guardPost();
        $payload = Request::json();
        $ids = $payload['ids'] ?? ($_POST['ids'] ?? []);
        if (!is_array($ids)) {
            json_out(['ok' => false], 422);
        }
        $clean = [];
        foreach ($ids as $id) {
            $clean[] = (int) $id;
        }
        Project::reorder($clean);
        Cache::flush();
        json_out(['ok' => true]);
    }

    public function toggle(string $id): void
    {
        $this->guardPost();
        $project = $this->find($id);
        $status = $project['status'] === 'published' ? 'draft' : 'published';
        Project::setStatus((int) $project['id'], $status);
        Cache::flush();
        if (wants_json()) {
            json_out(['ok' => true, 'status' => $status]);
        }
        $this->saved('Status updated.', '/admin/projects');
    }

    public function duplicate(string $id): void
    {
        $this->guardPost();
        $project = $this->find($id);
        $slug = $this->uniqueSlug($project['slug'] . '-copy');
        $newId = Project::create([
            'category_id' => $project['category_id'],
            'name' => $project['name'] . ' (copy)',
            'slug' => $slug,
            'short_description' => $project['short_description'],
            'long_description' => $project['long_description'],
            'url' => $project['url'],
            'technologies' => $project['technologies'],
            'year' => $project['year'],
            'status' => 'draft',
            'featured' => 0,
        ]);
        $sourceDir = Media::projectDir((int) $project['id']);
        $targetDir = Media::projectDir($newId);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        foreach (Project::images((int) $project['id']) as $image) {
            $stem = bin2hex(random_bytes(10));
            foreach (ImageProcessor::SIZES as $size => $unused) {
                foreach (['webp', 'jpg'] as $ext) {
                    $from = $sourceDir . '/' . $image['stem'] . '-' . $size . '.' . $ext;
                    $to = $targetDir . '/' . $stem . '-' . $size . '.' . $ext;
                    if (is_file($from)) {
                        copy($from, $to);
                    }
                }
            }
            Project::addImage(
                $newId,
                $stem,
                (string) $image['alt_text'],
                (int) $image['width'],
                (int) $image['height'],
                is_array($image['meta']) ? $image['meta'] : [],
                (int) $image['is_cover'] === 1
            );
        }
        $this->saved('Draft copy created.', '/admin/projects/' . $newId . '/edit');
    }

    public function upload(string $id): void
    {
        $this->guardPost();
        $project = $this->find($id);
        try {
            $image = $this->storeOne((int) $project['id'], $_FILES['image'] ?? [], (string) $project['name']);
        } catch (\Throwable $e) {
            json_out(['ok' => false, 'message' => $e->getMessage()], 422);
        }
        Cache::flush();
        json_out(['ok' => true, 'image' => $image]);
    }

    public function reorderImages(string $id): void
    {
        $this->guardPost();
        $project = $this->find($id);
        $payload = Request::json();
        $ids = $payload['ids'] ?? [];
        if (!is_array($ids)) {
            json_out(['ok' => false], 422);
        }
        $clean = array_map('intval', $ids);
        Project::reorderImages((int) $project['id'], $clean);
        Cache::flush();
        json_out(['ok' => true]);
    }

    public function cover(string $id, string $imageId): void
    {
        $this->guardPost();
        $project = $this->find($id);
        Project::setCover((int) $project['id'], (int) $imageId);
        Cache::flush();
        if (wants_json()) {
            json_out(['ok' => true]);
        }
        $this->saved('Cover updated.', '/admin/projects/' . $project['id'] . '/edit');
    }

    public function alt(string $id, string $imageId): void
    {
        $this->guardPost();
        $project = $this->find($id);
        $alt = Request::line('alt_text', 255);
        Project::updateAlt((int) $project['id'], (int) $imageId, $alt);
        Cache::flush();
        if (wants_json()) {
            json_out(['ok' => true]);
        }
        $this->saved('Alt text saved.', '/admin/projects/' . $project['id'] . '/edit');
    }

    public function deleteImage(string $id, string $imageId): void
    {
        $this->guardPost();
        $project = $this->find($id);
        $image = Project::image((int) $imageId, (int) $project['id']);
        if ($image !== null) {
            (new ImageProcessor())->deleteSet(Media::projectDir((int) $project['id']), (string) $image['stem']);
            Project::deleteImage((int) $project['id'], (int) $image['id']);
        }
        Cache::flush();
        if (wants_json()) {
            json_out(['ok' => true]);
        }
        $this->saved('Image deleted.', '/admin/projects/' . $project['id'] . '/edit');
    }

    /** @return array<string, mixed> */
    private function find(string $id): array
    {
        $project = Project::find((int) $id);
        if ($project === null) {
            http_response_code(404);
            exit('Project not found.');
        }
        return $project;
    }

    /** @return array{errors: array<string, string>, input: array<string, mixed>, row: array<string, mixed>} */
    private function payload(?int $id): array
    {
        $name = Request::line('name', 160);
        $slugInput = Request::line('slug', 180);
        $slug = $slugInput !== '' ? slugify($slugInput) : slugify($name);
        $short = Request::line('short_description', 300);
        $long = Request::string('long_description', 20000);
        $link = Request::line('url', 500);
        $year = Request::line('year', 4);
        $status = Request::line('status', 20);
        $featured = isset($_POST['featured']) ? 1 : 0;
        $technologies = tag_list((string) ($_POST['technologies'] ?? ''));
        $newCategory = Request::line('new_category', 80);
        $categoryId = (int) Request::line('category_id', 12);
        $input = [
            'name' => $name,
            'slug' => $slug,
            'short_description' => $short,
            'long_description' => $long,
            'url' => $link,
            'year' => $year,
            'status' => $status === '' ? 'draft' : $status,
            'featured' => $featured,
            'technologies' => implode(', ', $technologies),
            'category_id' => (string) $categoryId,
            'new_category' => $newCategory,
        ];
        $validator = (new Validator($input))
            ->required('name', 'Name')
            ->max('name', 160, 'Name')
            ->required('slug', 'Slug')
            ->slug('slug')
            ->required('short_description', 'Short description')
            ->max('short_description', 300, 'Short description')
            ->required('url', 'Link')
            ->httpUrl('url', 'Link')
            ->year('year')
            ->in('status', ['draft', 'published'], 'Status');
        $errors = $validator->errors();
        if ($slug !== '' && Project::slugExists($slug, $id)) {
            $errors['slug'] = 'That slug is already in use.';
        }
        if ($newCategory === '' && $categoryId > 0 && Category::find($categoryId) === null) {
            $errors['category_id'] = 'Choose a category.';
        }
        if ($errors === [] && $newCategory !== '') {
            $existing = Category::findByName($newCategory);
            $categoryId = $existing !== null ? (int) $existing['id'] : Category::create($newCategory);
        }
        return [
            'errors' => $errors,
            'input' => $input,
            'row' => [
                'category_id' => $categoryId > 0 ? $categoryId : null,
                'name' => $name,
                'slug' => $slug,
                'short_description' => $short,
                'long_description' => $long !== '' ? $long : null,
                'url' => $link,
                'technologies' => $technologies,
                'year' => $year !== '' ? (int) $year : null,
                'status' => $input['status'],
                'featured' => $featured,
            ],
        ];
    }

    private function uniqueSlug(string $base): string
    {
        $slug = slugify($base);
        $try = $slug;
        $i = 2;
        while (Project::slugExists($try)) {
            $try = $slug . '-' . $i;
            $i++;
        }
        return $try;
    }

    private function acceptUploads(int $projectId, string $name): void
    {
        if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'] ?? null)) {
            return;
        }
        $files = $_FILES['images'];
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ((int) $files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
            try {
                $this->storeOne($projectId, $file, $name);
            } catch (\Throwable $e) {
                Session::flash('error', $e->getMessage());
            }
        }
    }

    /** @param array<string, mixed> $file
     *  @return array<string, mixed>
     */
    private function storeOne(int $projectId, array $file, string $projectName): array
    {
        $max = (int) (config('app')['upload_max_images'] ?? 12);
        if (Project::imageCount($projectId) >= $max) {
            throw new \RuntimeException('This project already has the maximum number of images.');
        }
        $stored = (new ImageProcessor())->storeUpload($file, Media::projectDir($projectId));
        $cover = Project::imageCount($projectId) === 0;
        $alt = $projectName;
        $imageId = Project::addImage(
            $projectId,
            $stored['stem'],
            $alt,
            $stored['width'],
            $stored['height'],
            $stored['meta'],
            $cover
        );
        return [
            'id' => $imageId,
            'alt' => $alt,
            'cover' => $cover,
            'url' => Media::baseUrl($projectId, $stored['stem']) . '-thumb.webp',
        ];
    }

    private function removeFiles(int $projectId): void
    {
        (new ImageProcessor())->deleteTree(Media::projectDir($projectId));
        $dir = Media::projectDir($projectId);
        if (is_dir($dir)) {
            @rmdir($dir);
        }
    }
}
