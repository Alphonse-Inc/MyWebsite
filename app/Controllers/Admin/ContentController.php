<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Cache;
use App\Core\ImageProcessor;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Testimonial;
use App\Services\Media;

final class ContentController extends AdminController
{
    public function profileForm(): void
    {
        $this->admin('admin/profile', [
            'pageTitle' => 'Profile',
            'profile' => Profile::get(),
        ]);
    }

    public function profileSave(): void
    {
        $this->guardPost();
        $current = Profile::get();
        if ($current === null) {
            http_response_code(500);
            exit('Profile is missing. Run the seeder.');
        }
        $titles = tag_list(str_replace("\n", ',', (string) ($_POST['titles'] ?? '')));
        $input = [
            'full_name' => Request::line('full_name', 190),
            'display_name' => Request::line('display_name', 120),
            'handle' => Request::line('handle', 80),
            'tagline' => Request::line('tagline', 500),
            'bio' => Request::string('bio', 5000),
            'photo_alt' => Request::line('photo_alt', 255),
            'availability_label' => Request::line('availability_label', 120),
            'quote' => Request::string('quote', 1000),
            'quote_author' => Request::line('quote_author', 120),
        ];
        $validator = (new Validator($input))
            ->required('full_name', 'Full name')
            ->required('display_name', 'Display name')
            ->required('bio', 'Bio')
            ->required('quote', 'Quote');
        if ($validator->fails() || $titles === []) {
            $errors = $validator->errors();
            if ($titles === []) {
                $errors['titles'] = 'Add at least one typed title.';
            }
            $this->backWithErrors('/admin/profile', $errors, $input);
        }
        $photoPath = $current['photo_path'];
        $photoMeta = $current['photo_meta'];
        $video = $current['hero_video_path'];
        $poster = $current['hero_poster_path'];
        $posterMeta = $current['hero_poster_meta'];
        $processor = new ImageProcessor();
        $dir = Media::root() . '/profile';
        if (isset($_FILES['photo']) && (int) ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $stored = $processor->storeUpload($_FILES['photo'], $dir);
            $photoPath = 'profile/' . $stored['stem'];
            $photoMeta = $stored['meta'];
        }
        if (isset($_FILES['poster']) && (int) ($_FILES['poster']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $stored = $processor->storeUpload($_FILES['poster'], $dir);
            $poster = 'profile/' . $stored['stem'];
            $posterMeta = $stored['meta'];
        }
        if (isset($_FILES['video']) && (int) ($_FILES['video']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $video = $this->storeVideo($_FILES['video'], $dir);
        }
        if (isset($_POST['remove_video'])) {
            $this->unlinkUpload($video);
            $video = null;
        }
        Profile::save([
            'full_name' => $input['full_name'],
            'display_name' => $input['display_name'],
            'handle' => $input['handle'],
            'titles' => $titles,
            'tagline' => $input['tagline'],
            'bio' => $input['bio'],
            'photo_path' => $photoPath,
            'photo_alt' => $input['photo_alt'],
            'photo_meta' => $photoMeta,
            'hero_video_path' => $video,
            'hero_poster_path' => $poster,
            'hero_poster_meta' => $posterMeta,
            'availability_label' => $input['availability_label'],
            'available' => isset($_POST['available']) ? 1 : 0,
            'quote' => $input['quote'],
            'quote_author' => $input['quote_author'],
        ]);
        $this->saved('Profile saved.', '/admin/profile');
    }

    public function services(): void
    {
        $this->admin('admin/services', ['pageTitle' => 'Services', 'rows' => Service::allRows()]);
    }

    public function serviceSave(): void
    {
        $this->guardPost();
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'name' => Request::line('name', 120),
            'description' => Request::string('description', 2000),
            'icon' => Request::line('icon', 40) ?: 'code',
            'sort_order' => (int) Request::line('sort_order', 6),
            'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
        ];
        if ($data['name'] === '' || $data['description'] === '') {
            $this->backWithErrors('/admin/services', ['name' => 'Name and description are required.'], $data);
        }
        if ($id > 0) {
            Service::update($id, $data);
        } else {
            Service::create($data);
        }
        $this->saved('Service saved.', '/admin/services');
    }

    public function serviceDelete(string $id): void
    {
        $this->guardPost();
        Service::delete((int) $id);
        $this->saved('Service deleted.', '/admin/services');
    }

    public function serviceReorder(): void
    {
        $this->reorderTable('services', static function (array $ids): void {
            Service::reorder($ids);
        });
    }

    public function experience(): void
    {
        $this->admin('admin/experience', ['pageTitle' => 'Experience', 'rows' => Experience::allRows()]);
    }

    public function experienceSave(): void
    {
        $this->guardPost();
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->timelineInput();
        if ($data['role'] === '' || $data['organization'] === '') {
            $this->backWithErrors('/admin/experience', ['role' => 'Role and organization are required.'], $data);
        }
        if ($id > 0) {
            Experience::update($id, $data);
        } else {
            Experience::create($data);
        }
        $this->saved('Experience saved.', '/admin/experience');
    }

    public function experienceDelete(string $id): void
    {
        $this->guardPost();
        Experience::delete((int) $id);
        $this->saved('Experience deleted.', '/admin/experience');
    }

    public function education(): void
    {
        $this->admin('admin/education', ['pageTitle' => 'Education', 'rows' => Education::allRows()]);
    }

    public function educationSave(): void
    {
        $this->guardPost();
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'title' => Request::line('title', 190),
            'institution' => Request::line('institution', 190),
            'location' => Request::line('location', 160),
            'start_label' => Request::line('start_label', 40),
            'end_label' => Request::line('end_label', 40),
            'description' => Request::string('description', 2000),
            'sort_order' => (int) Request::line('sort_order', 6),
            'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
        ];
        if ($data['title'] === '' || $data['institution'] === '') {
            $this->backWithErrors('/admin/education', ['title' => 'Title and institution are required.'], $data);
        }
        if ($id > 0) {
            Education::update($id, $data);
        } else {
            Education::create($data);
        }
        $this->saved('Education saved.', '/admin/education');
    }

    public function educationDelete(string $id): void
    {
        $this->guardPost();
        Education::delete((int) $id);
        $this->saved('Education deleted.', '/admin/education');
    }

    public function skills(): void
    {
        $this->admin('admin/skills', ['pageTitle' => 'Skills', 'groups' => Skill::grouped()]);
    }

    public function skillGroupSave(): void
    {
        $this->guardPost();
        $id = (int) ($_POST['id'] ?? 0);
        $type = Request::line('display_type', 16);
        if (!in_array($type, ['bar', 'ring', 'tag'], true)) {
            $type = 'bar';
        }
        $data = [
            'name' => Request::line('name', 80),
            'display_type' => $type,
            'sort_order' => (int) Request::line('sort_order', 6),
        ];
        if ($data['name'] === '') {
            $this->backWithErrors('/admin/skills', ['name' => 'Group name is required.'], $data);
        }
        if ($id > 0) {
            Skill::updateGroup($id, $data);
        } else {
            Skill::createGroup($data);
        }
        $this->saved('Skill group saved.', '/admin/skills');
    }

    public function skillGroupDelete(string $id): void
    {
        $this->guardPost();
        Skill::deleteGroup((int) $id);
        $this->saved('Skill group deleted.', '/admin/skills');
    }

    public function skillSave(): void
    {
        $this->guardPost();
        $id = (int) ($_POST['id'] ?? 0);
        $levelRaw = trim((string) ($_POST['level'] ?? ''));
        $data = [
            'group_id' => (int) ($_POST['group_id'] ?? 0),
            'name' => Request::line('name', 120),
            'level' => $levelRaw === '' ? null : (int) $levelRaw,
            'sort_order' => (int) Request::line('sort_order', 6),
        ];
        if ($data['name'] === '' || Skill::findGroup($data['group_id']) === null) {
            $this->backWithErrors('/admin/skills', ['name' => 'Skill name and group are required.'], $data);
        }
        if ($data['level'] !== null && ($data['level'] < 0 || $data['level'] > 100)) {
            $this->backWithErrors('/admin/skills', ['level' => 'Level must be 0 to 100, or empty for a tag.'], $data);
        }
        if ($id > 0) {
            Skill::update($id, $data);
        } else {
            Skill::create($data);
        }
        $this->saved('Skill saved.', '/admin/skills');
    }

    public function skillDelete(string $id): void
    {
        $this->guardPost();
        Skill::delete((int) $id);
        $this->saved('Skill deleted.', '/admin/skills');
    }

    public function socials(): void
    {
        $this->admin('admin/socials', ['pageTitle' => 'Social links', 'rows' => SocialLink::allRows()]);
    }

    public function socialSave(): void
    {
        $this->guardPost();
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'platform' => Request::line('platform', 40),
            'label' => Request::line('label', 80),
            'url' => Request::line('url', 500),
            'sort_order' => (int) Request::line('sort_order', 6),
            'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
        ];
        $validator = (new Validator($data))->required('label', 'Label')->required('url', 'URL')->httpUrl('url');
        if ($validator->fails()) {
            $this->backWithErrors('/admin/socials', $validator->errors(), $data);
        }
        if ($id > 0) {
            SocialLink::update($id, $data);
        } else {
            SocialLink::create($data);
        }
        $this->saved('Social link saved.', '/admin/socials');
    }

    public function socialDelete(string $id): void
    {
        $this->guardPost();
        SocialLink::delete((int) $id);
        $this->saved('Social link deleted.', '/admin/socials');
    }

    public function testimonials(): void
    {
        $this->admin('admin/testimonials', ['pageTitle' => 'Testimonials', 'rows' => Testimonial::allRows()]);
    }

    public function testimonialSave(): void
    {
        $this->guardPost();
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'author_name' => Request::line('author_name', 120),
            'author_role' => Request::line('author_role', 160),
            'quote' => Request::string('quote', 2000),
            'photo_path' => null,
            'sort_order' => (int) Request::line('sort_order', 6),
            'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
        ];
        if ($data['author_name'] === '' || $data['quote'] === '') {
            $this->backWithErrors('/admin/testimonials', ['quote' => 'Name and quote are required.'], $data);
        }
        if ($id > 0) {
            $existing = Testimonial::find($id);
            $data['photo_path'] = $existing['photo_path'] ?? null;
            Testimonial::update($id, $data);
        } else {
            Testimonial::create($data);
        }
        $this->saved('Testimonial saved.', '/admin/testimonials');
    }

    public function testimonialDelete(string $id): void
    {
        $this->guardPost();
        Testimonial::delete((int) $id);
        $this->saved('Testimonial deleted.', '/admin/testimonials');
    }

    public function contactForm(): void
    {
        $this->admin('admin/contact', ['pageTitle' => 'Contact details', 'settings' => Setting::all()]);
    }

    public function contactSave(): void
    {
        $this->guardPost();
        $map = Request::line('map_embed', 2000);
        if ($map !== '' && !$this->safeMap($map)) {
            $this->backWithErrors('/admin/contact', ['map_embed' => 'Map embed must be an https Google Maps or OpenStreetMap URL.'], $_POST);
        }
        $whatsapp = Request::line('contact_whatsapp', 300);
        if ($whatsapp !== '' && filter_var($whatsapp, FILTER_VALIDATE_URL) === false) {
            $this->backWithErrors('/admin/contact', ['contact_whatsapp' => 'WhatsApp link must be a valid URL.'], $_POST);
        }
        Setting::putMany([
            'contact_email' => Request::line('contact_email', 190),
            'contact_phone' => Request::line('contact_phone', 40),
            'contact_phone_e164' => preg_replace('/\D+/', '', Request::line('contact_phone_e164', 20)) ?? '',
            'contact_location' => Request::line('contact_location', 160),
            'contact_whatsapp' => $whatsapp,
            'freelance_status' => Request::line('freelance_status', 80),
            'map_embed' => $map,
            'label_freelance' => Request::line('label_freelance', 40),
            'label_phone' => Request::line('label_phone', 40),
            'label_email' => Request::line('label_email', 40),
            'label_address' => Request::line('label_address', 40),
            'label_whatsapp' => Request::line('label_whatsapp', 40),
        ]);
        $this->saved('Contact details saved.', '/admin/contact');
    }

    public function cvForm(): void
    {
        $this->admin('admin/cv', [
            'pageTitle' => 'CV',
            'path' => Setting::get('cv_path', ''),
            'downloads' => Setting::get('cv_downloads', '0'),
        ]);
    }

    public function cvSave(): void
    {
        $this->guardPost();
        if (!isset($_FILES['cv']) || (int) ($_FILES['cv']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->backWithErrors('/admin/cv', ['cv' => 'Choose a PDF to upload.'], []);
        }
        $file = $_FILES['cv'];
        if ((int) $file['size'] > 8 * 1024 * 1024) {
            $this->backWithErrors('/admin/cv', ['cv' => 'The PDF must be 8MB or smaller.'], []);
        }
        $tmp = (string) $file['tmp_name'];
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if ($mime !== 'application/pdf') {
            $this->backWithErrors('/admin/cv', ['cv' => 'Only a PDF file is accepted.'], []);
        }
        $dir = Media::root() . '/cv';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $target = $dir . '/cv.pdf';
        if (!move_uploaded_file($tmp, $target)) {
            $this->backWithErrors('/admin/cv', ['cv' => 'The PDF could not be stored.'], []);
        }
        Setting::put('cv_path', 'cv/cv.pdf');
        $this->saved('CV uploaded.', '/admin/cv');
    }

    public function cvDelete(): void
    {
        $this->guardPost();
        $path = Media::root() . '/cv/cv.pdf';
        if (is_file($path)) {
            unlink($path);
        }
        Setting::put('cv_path', '');
        $this->saved('CV removed.', '/admin/cv');
    }

    /** @return array<string, mixed> */
    private function timelineInput(): array
    {
        return [
            'role' => Request::line('role', 160),
            'organization' => Request::line('organization', 160),
            'location' => Request::line('location', 160),
            'start_label' => Request::line('start_label', 40),
            'end_label' => Request::line('end_label', 40),
            'description' => Request::string('description', 4000),
            'sort_order' => (int) Request::line('sort_order', 6),
            'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
        ];
    }

    /** @param callable(list<int>): void $writer */
    private function reorderTable(string $redirect, callable $writer): void
    {
        $this->guardPost();
        $payload = Request::json();
        $ids = $payload['ids'] ?? ($_POST['ids'] ?? []);
        if (!is_array($ids)) {
            json_out(['ok' => false], 422);
        }
        $writer(array_map('intval', $ids));
        Cache::flush();
        if (wants_json()) {
            json_out(['ok' => true]);
        }
        $this->saved('Order saved.', '/admin/' . $redirect);
    }

    /** @param array<string, mixed> $file */
    private function storeVideo(array $file, string $dir): string
    {
        if ((int) $file['size'] > 30 * 1024 * 1024) {
            throw new \RuntimeException('Video must be 30MB or smaller.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        if ($mime !== 'video/mp4') {
            throw new \RuntimeException('Hero video must be an MP4 file.');
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = 'hero-' . bin2hex(random_bytes(6)) . '.mp4';
        $target = $dir . '/' . $name;
        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            throw new \RuntimeException('Video could not be stored.');
        }
        return 'profile/' . $name;
    }

    private function unlinkUpload(?string $relative): void
    {
        if ($relative === null || $relative === '') {
            return;
        }
        $path = Media::root() . '/' . ltrim($relative, '/');
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function safeMap(string $url): bool
    {
        $parts = parse_url($url);
        if (!is_array($parts) || ($parts['scheme'] ?? '') !== 'https') {
            return false;
        }
        $host = strtolower((string) ($parts['host'] ?? ''));
        return in_array($host, ['www.google.com', 'maps.google.com', 'www.openstreetmap.org', 'openstreetmap.org'], true);
    }
}
