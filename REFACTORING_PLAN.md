# 🔄 CODE REFACTORING PLAN - Clean Code Architecture Implementation

**Status**: IN PLANNING  
**Date**: November 25, 2025  
**Objective**: Implement clean separation of concerns across the entire application

---

## 📋 REFACTORING PHASES

### Phase 1: Audit & Analysis (CURRENT)

-   [x] Scan all Blade files
-   [x] Identify filter components
-   [x] Check JS/CSS structure
-   [x] Map controller validations
-   [ ] Document current state

### Phase 2: Extract Filters (HIGH PRIORITY)

**Target**: Siswa Index, Riwayat Index  
Files:

-   `resources/views/siswa/index.blade.php` (231 lines)
-   `public/js/pages/siswa/index.js` (refactor & expand)
-   `public/css/pages/siswa/index.css` (refactor & expand)
-   `resources/views/riwayat/index.blade.php` (255 lines)
-   `public/js/pages/riwayat/index.js` (create)
-   `public/css/pages/riwayat/index.css` (create)

### Phase 3: Extract Components (MEDIUM PRIORITY)

Target: Create reusable filter components

-   Filter form partials
-   Shared filter styling
-   Common filter logic

### Phase 4: Validate Controllers (HIGH PRIORITY)

Review:

-   `SiswaController.php`
-   `RiwayatPelanggaranController.php`
-   `UsersController.php`
-   Add validation/sanitization

### Phase 5: Documentation

Update architecture docs with new patterns

---

## 🎯 CURRENT ISSUES IDENTIFIED

### Siswa Index (`index.blade.php` - 231 lines)

**Problem**:

-   Filter logic mixed with HTML markup
-   Multiple inline onchange handlers
-   Conditional logic scattered

**Solution**:

```
Blade (clean markup only)
  ↓ calls CSS from
public/css/pages/siswa/filters.css
  ↓ calls JS from
public/js/pages/siswa/filters.js
  ↓ JS handles all interactions
```

### Riwayat Index (`index.blade.php` - 255 lines)

**Problem**:

-   Complex filter form (date range, select, search)
-   Same issues as siswa

**Solution**: Extract to separate filter modules

### Controllers

**Need to check**:

-   Input validation
-   Sanitization
-   Authorization checks
-   Error handling

---

## 📊 REFACTORING BREAKDOWN

| Component       | Blade Lines | Extract To                 | Priority |
| --------------- | ----------- | -------------------------- | -------- |
| Siswa Filters   | ~80 lines   | `siswa/filters.js` + CSS   | HIGH     |
| Riwayat Filters | ~100 lines  | `riwayat/filters.js` + CSS | HIGH     |
| Table Display   | ~150 lines  | Keep in blade              | LOW      |
| Modals          | ~30 lines   | Extract later              | MEDIUM   |
| Search Logic    | Mixed       | `search-module.js`         | MEDIUM   |

---

## ✅ NEW STRUCTURE AFTER REFACTORING

```
resources/views/siswa/
├── index.blade.php              ← Markup ONLY (~150 lines)
│   @include('components/siswa_filter_form')  ← Filter form partial
│   <!-- Table HTML -->
│
public/js/pages/siswa/
├── index.js                     ← Page initialization
├── filters.js                   ← Filter logic (NEW)
├── table.js                     ← Table interactions (NEW)
└── create.js, edit.js, bulk_create.js

public/css/pages/siswa/
├── index.css                    ← Page base styling
├── filters.css                  ← Filter styling (NEW)
├── table.css                    ← Table styling (NEW)
└── create.css, edit.css
```

---

## 🔍 CONTROLLER VALIDATION CHECKLIST

-   [ ] SiswaController@index - validate request, sanitize inputs
-   [ ] SiswaController@create - check authorization
-   [ ] SiswaController@store - validate all fields
-   [ ] RiwayatPelanggaranController@index - validate dates, sanitize
-   [ ] RiwayatPelanggaranController@store - validate relations
-   [ ] UsersController - all CRUD operations
-   [ ] AuditController - validate scope, sanitize IDs

---

## 🎓 IMPLEMENTATION STRATEGY

### Step 1: Extract Filter Form Partial

Create: `resources/views/components/siswa/filter-form.blade.php`

-   Move entire filter form HTML
-   Remove all JS logic
-   Keep CSS classes

### Step 2: Create Filter JS Module

Create: `public/js/pages/siswa/filters.js`

-   IIFE pattern
-   Auto-submit handlers
-   State management
-   Validation

### Step 3: Create Filter CSS Module

Refactor: `public/css/pages/siswa/filters.css` (NEW)

-   Extract all filter-related styles
-   BEM convention
-   Mobile responsive

### Step 4: Update Main Blade

Update: `resources/views/siswa/index.blade.php`

-   Remove filter form HTML → use @include
-   Remove inline scripts
-   Add script import in @section('scripts')

### Step 5: Validate Controller

Update: `app/Http/Controllers/SiswaController.php`

-   Add input validation
-   Sanitize request data
-   Add error handling

---

## 📝 NEXT ACTIONS

1. **Create filter form partial** (siswa)
2. **Extract filter.js module** (siswa)
3. **Create filters.css** (siswa)
4. **Update siswa index.blade.php**
5. **Validate SiswaController**
6. **Repeat for riwayat, pelanggaran, etc.**

---

**Estimated Timeline**: 2-3 hours for full implementation  
**Risk Level**: LOW (backward compatible)  
**Testing Required**: Manual testing all filters
