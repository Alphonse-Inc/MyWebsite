(function () {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const source = document.querySelector('[data-slug-source]');
  const target = document.querySelector('[data-slug-target]');
  if (source && target && target.value === '') {
    source.addEventListener('input', function () {
      if (target.dataset.touched === '1') return;
      target.value = source.value.toLowerCase().replace(/&/g, ' and ').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    });
    target.addEventListener('input', function () { target.dataset.touched = '1'; });
  }

  document.querySelectorAll('[data-count]').forEach(function (field) {
    const max = Number(field.getAttribute('data-count') || 300);
    const label = field.parentElement?.querySelector('[data-counter]');
    const update = function () {
      if (label) label.textContent = String(field.value.length);
      if (field.value.length > max) field.value = field.value.slice(0, max);
    };
    field.addEventListener('input', update);
    update();
  });

  const preview = document.querySelector('[data-url-preview]');
  const urlField = document.querySelector('#url');
  if (preview && urlField) {
    urlField.addEventListener('input', function () { preview.setAttribute('href', urlField.value || '#'); });
  }

  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!window.confirm(form.getAttribute('data-confirm'))) event.preventDefault();
    });
  });

  function postOrder(url, ids) {
    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ ids: ids, _token: token })
    }).then(function (response) {
      if (!response.ok && window.showToast) showToast('Could not save the new order.', 'error');
      else if (window.showToast) showToast('Order saved.', 'success');
    });
  }

  document.querySelectorAll('[data-reorder]').forEach(function (holder) {
    const url = holder.getAttribute('data-reorder');
    let dragged = null;
    holder.querySelectorAll('[draggable="true"]').forEach(function (row) {
      row.addEventListener('dragstart', function () { dragged = row; });
      row.addEventListener('dragover', function (event) {
        event.preventDefault();
        if (!dragged || dragged === row) return;
        const box = row.getBoundingClientRect();
        const after = event.clientY > box.top + box.height / 2;
        holder.querySelector('tbody') ? holder.querySelector('tbody').insertBefore(dragged, after ? row.nextSibling : row) : holder.insertBefore(dragged, after ? row.nextSibling : row);
      });
      row.addEventListener('drop', function (event) {
        event.preventDefault();
        const ids = Array.from(holder.querySelectorAll('[data-id]')).map(function (item) { return item.getAttribute('data-id'); });
        postOrder(url, ids);
      });
    });
  });

  const uploader = document.querySelector('[data-uploader]');
  if (uploader) {
    const zone = uploader.querySelector('[data-dropzone]');
    const bar = uploader.querySelector('[data-progress]');
    const list = uploader.querySelector('[data-image-list]');
    const endpoint = uploader.getAttribute('data-upload-url');
    function upload(file) {
      const body = new FormData();
      body.append('_token', token);
      body.append('image', file);
      const xhr = new XMLHttpRequest();
      xhr.open('POST', endpoint);
      xhr.setRequestHeader('X-CSRF-Token', token);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.upload.onprogress = function (event) {
        if (!bar || !event.lengthComputable) return;
        bar.style.width = Math.round((event.loaded / event.total) * 100) + '%';
      };
      xhr.onload = function () {
        if (bar) bar.style.width = '0';
        let data = {};
        try { data = JSON.parse(xhr.responseText); } catch (e) { data = {}; }
        if (xhr.status >= 200 && xhr.status < 300 && data.ok) {
          if (window.showToast) showToast('Image uploaded.', 'success');
          window.location.reload();
        } else if (window.showToast) {
          showToast(data.message || 'Upload failed.', 'error');
        }
      };
      xhr.send(body);
    }
    if (zone) {
      zone.addEventListener('click', function () {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/jpeg,image/png,image/webp,image/gif';
        input.multiple = true;
        input.addEventListener('change', function () { Array.from(input.files || []).forEach(upload); });
        input.click();
      });
      ['dragover', 'dragenter'].forEach(function (name) {
        zone.addEventListener(name, function (event) { event.preventDefault(); zone.classList.add('is-over'); });
      });
      zone.addEventListener('dragleave', function () { zone.classList.remove('is-over'); });
      zone.addEventListener('drop', function (event) {
        event.preventDefault();
        zone.classList.remove('is-over');
        Array.from(event.dataTransfer.files || []).forEach(upload);
      });
    }
    if (list) {
      const reorder = uploader.getAttribute('data-reorder-url');
      let dragged = null;
      list.querySelectorAll('[draggable="true"]').forEach(function (item) {
        item.addEventListener('dragstart', function () { dragged = item; });
        item.addEventListener('dragover', function (event) {
          event.preventDefault();
          if (!dragged || dragged === item) return;
          list.insertBefore(dragged, item);
        });
        item.addEventListener('drop', function (event) {
          event.preventDefault();
          const ids = Array.from(list.querySelectorAll('[data-id]')).map(function (node) { return node.getAttribute('data-id'); });
          postOrder(reorder, ids);
        });
      });
    }
  }
})();
