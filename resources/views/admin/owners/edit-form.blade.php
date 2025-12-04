<form id="editOwnerFormAjax" action="{{ route('admin.owners.update', $owner) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="edit_name">Naam Eigenaar</label>
        <input
            type="text"
            id="edit_name"
            name="name"
            class="form-control"
            value="{{ old('name', $owner->name) }}"
            required
        >
    </div>

    <div class="form-group">
        <label for="edit_email">Email</label>
        <input
            type="email"
            id="edit_email"
            name="email"
            class="form-control"
            value="{{ old('email', $owner->email) }}"
            required
        >
    </div>

    <div class="form-group">
        <label for="edit_hotel_name">Hotelnaam</label>
        <input
            type="text"
            id="edit_hotel_name"
            name="hotel_name"
            class="form-control"
            value="{{ old('hotel_name', $owner->hotels->first()->name ?? '') }}"
            required
        >
    </div>

    <div class="overlay-actions">
        <button type="button" class="neu-button-secondary overlay-close" data-overlay="editOwnerOverlay">Annuleren</button>
        <button type="submit" class="neu-button-primary">Wijzigingen Opslaan</button>
    </div>
</form>

<script>
document.getElementById('editOwnerFormAjax').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Opslaan...';

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close overlay
            const overlay = document.getElementById('editOwnerOverlay');
            overlay.classList.remove('active');
            document.body.style.overflow = '';

            // Reload page to show updated data
            location.reload();
        } else {
            alert('Er is een fout opgetreden. Probeer het opnieuw.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden. Probeer het opnieuw.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Wijzigingen Opslaan';
    });
});
</script>

