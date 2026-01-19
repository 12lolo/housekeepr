# AJAX Forms Guide - No More Page Reloads!

## What Was Implemented

I've added an AJAX form system using Alpine.js (already in your project) that makes CRUD operations feel instant without page reloads.

### Features
✅ **No page reloads** - Forms submit via AJAX
✅ **Toast notifications** - Success/error messages slide in from top-right
✅ **Loading states** - Buttons show "Bezig met opslaan..." while submitting
✅ **Validation errors** - Show inline under fields with red styling
✅ **Auto-close modals** - Modals close automatically on success
✅ **Smooth animations** - Professional feel with fade-in toasts

### What's Already Done
- ✅ **Booking Create Form** - Fully converted to AJAX
- ✅ **BookingController** - Already supports AJAX responses (lines 93-98)
- ✅ **Toast System** - Styled for both light and dark mode
- ✅ **Error Handling** - Validation errors display under fields

## How to Convert Other Forms

### Step 1: Update the Modal Wrapper

**Before:**
```blade
<div class="neu-modal-overlay" id="roomCreateModal">
```

**After:**
```blade
<div class="neu-modal-overlay" id="roomCreateModal" x-data="ajaxForm('roomCreateModal')">
```

### Step 2: Update the Form Tag

**Before:**
```blade
<form action="{{ route('owner.rooms.store') }}" method="POST">
```

**After:**
```blade
<form action="{{ route('owner.rooms.store') }}" method="POST"
      @submit="submitForm($event)" :class="{ 'form-loading': loading }">
```

### Step 3: Add Error Display to Input Fields

**Before:**
```blade
<div class="neu-form-group">
    <label for="room_number" class="neu-label">Kamernummer *</label>
    <input type="text" name="room_number" class="neu-input" required>
</div>
```

**After:**
```blade
<div class="neu-form-group">
    <label for="room_number" class="neu-label">Kamernummer *</label>
    <input type="text" name="room_number" class="neu-input" required
           :class="{ 'has-error': hasError('room_number') }">
    <span x-show="hasError('room_number')" x-text="getError('room_number')" class="error-text"></span>
</div>
```

### Step 4: Update Submit Button

**Before:**
```blade
<button type="submit" class="neu-button-primary">Opslaan</button>
```

**After:**
```blade
<button type="submit" class="neu-button-primary" :disabled="loading">
    <span x-show="!loading">Opslaan</span>
    <span x-show="loading">Bezig met opslaan...</span>
</button>
```

### Step 5: Update Controller (if needed)

Most controllers already have AJAX support. Check if your controller has this:

```php
if ($request->ajax() || $request->wantsJson()) {
    return response()->json([
        'success' => true,
        'message' => 'Succesvol opgeslagen!',
        'data' => $model,
    ]);
}
```

If not, add it before the `return redirect()` statement.

## Quick Conversion Checklist

For each form (room-create, room-edit, cleaner-create, cleaner-edit, etc.):

- [ ] Add `x-data="ajaxForm('modalId')"` to modal wrapper
- [ ] Add `@submit="submitForm($event)"` to form tag
- [ ] Add `:class="{ 'form-loading': loading }"` to form tag
- [ ] Add `:class="{ 'has-error': hasError('field') }"` to all inputs
- [ ] Add `<span x-show="hasError('field')" x-text="getError('field')" class="error-text"></span>` under each input
- [ ] Update submit button with loading state
- [ ] Verify controller has AJAX response handling

## Example: Complete Room Create Form

```blade
<div class="neu-modal-overlay" id="roomCreateModal" x-data="ajaxForm('roomCreateModal')">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Nieuwe Kamer</h3>
            <button class="neu-modal-close" type="button" onclick="closeModal('roomCreateModal')">×</button>
        </div>

        <div class="neu-modal-body">
            <form action="{{ route('owner.rooms.store') }}" method="POST"
                  @submit="submitForm($event)" :class="{ 'form-loading': loading }">
                @csrf

                <div class="neu-form-group">
                    <label for="room_number" class="neu-label">Kamernummer *</label>
                    <input type="text" name="room_number" class="neu-input" required
                           :class="{ 'has-error': hasError('room_number') }">
                    <span x-show="hasError('room_number')" x-text="getError('room_number')" class="error-text"></span>
                </div>

                <div class="neu-form-group">
                    <label for="type" class="neu-label">Type *</label>
                    <select name="type" class="neu-input" required
                            :class="{ 'has-error': hasError('type') }">
                        <option value="">Selecteer type</option>
                        <option value="single">Eenpersoonskamer</option>
                        <option value="double">Tweepersoonskamer</option>
                        <option value="suite">Suite</option>
                    </select>
                    <span x-show="hasError('type')" x-text="getError('type')" class="error-text"></span>
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary"
                            onclick="closeModal('roomCreateModal')" :disabled="loading">
                        Annuleren
                    </button>
                    <button type="submit" class="neu-button-primary" :disabled="loading">
                        <span x-show="!loading">Kamer toevoegen</span>
                        <span x-show="loading">Bezig met opslaan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

## Delete Actions

For delete buttons/links, replace the old approach with:

**Before:**
```blade
<form action="{{ route('owner.rooms.destroy', $room) }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit">Verwijderen</button>
</form>
```

**After:**
```blade
<button type="button"
        onclick="confirmDelete('{{ route('owner.rooms.destroy', $room) }}', '{{ $room->room_number }}')">
    Verwijderen
</button>
```

The `confirmDelete()` function automatically:
- Shows confirmation dialog
- Sends DELETE request via AJAX
- Shows success/error toast
- Reloads page to show changes

## Testing

1. **Open your site** and go to the dashboard
2. **Click "Nieuwe Boeking"**
3. **Fill out the form** and submit
4. **Watch for:**
   - Button changes to "Bezig met opslaan..."
   - Modal closes automatically
   - Green toast notification appears (top-right)
   - Page reloads smoothly showing the new booking

## Troubleshooting

### Form still refreshes page
- Check if `x-data="ajaxForm('modalId')"` is on the modal wrapper
- Check if `@submit="submitForm($event)"` is on the form tag
- Check browser console for JavaScript errors

### No toast notification
- Clear browser cache (Ctrl+Shift+R)
- Check if `/build/assets/app-*.js` loaded correctly
- Check browser console for errors

### Validation errors don't show
- Ensure `:class="{ 'has-error': hasError('field_name') }"` uses exact field name from validation rules
- Check if error span exists: `<span x-show="hasError('field_name')"...`

### Controller not returning JSON
- Ensure controller has `if ($request->ajax() || $request->wantsJson())` check
- Return `response()->json([...])` for AJAX requests
- Return `redirect()` for non-AJAX requests (fallback)

## Next Steps

Convert these forms to AJAX (in order of priority):

1. ✅ **booking-create.blade.php** - DONE
2. **booking-edit.blade.php** - Same pattern
3. **room-create.blade.php** - Same pattern
4. **room-edit.blade.php** - Same pattern
5. **cleaner-create.blade.php** - Same pattern (already has availability checkboxes)
6. **cleaner-edit.blade.php** - Same pattern
7. **issue-create.blade.php** - Same pattern

Each conversion takes about 5 minutes once you get the hang of it!

## Benefits

- **Better UX** - No jarring page reloads
- **Faster** - Only data transferred, not entire page
- **Professional** - Toast notifications feel modern
- **Mobile-friendly** - Less data usage, faster on slow connections
- **Error handling** - Inline errors are clearer than full page refresh

---

**Files involved:**
- `resources/js/ajax-forms.js` - Main AJAX logic
- `resources/scss/components/_toast.scss` - Toast notification styles
- `resources/views/owner/panels/booking-create.blade.php` - Example implementation
