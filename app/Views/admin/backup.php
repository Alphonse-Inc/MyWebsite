<p class="max-w-xl text-sm leading-relaxed text-muted">Download a zip with a SQL dump, a JSON copy of every table, and the uploads folder.</p>
<form class="mt-6" method="post" action="<?= e(url('/admin/backup')) ?>">
    <?= csrf_field() ?>
    <button class="btn-primary" type="submit">Download backup</button>
</form>
