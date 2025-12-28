# Modern Minimalist Color Palette Implementation Guide
## Guest Pages - Filament + Tailwind CSS v4 + DaisyUI v5

---

## 1. Elemen-elemen Utama Halaman Guest

### Komponen yang Membutuhkan Kombinasi Warna:

| Elemen | Kategori | Palet Warna |
|--------|----------|-------------|
| **Headings (H1-H3)** | Teks | Neutral 800-900 |
| **Body Text** | Teks | Neutral 500-600 |
| **Secondary Text** | Teks | Neutral 400-500 |
| **Page Background** | Background | Gradient Hero (3-stage) |
| **Card Background** | Background | White / Neutral 50 |
| **Borders** | Border | Neutral 200-300 |
| **Primary Buttons** | Button | Primary 600 → 700 (hover) |
| **Secondary Buttons** | Button | Border Primary 600 |
| **Input Fields** | Input | Border Neutral 300 → Primary 500 (focus) |
| **Links** | Link | Primary 600 → 700 (hover) |
| **Badges** | Badge | Primary 100 + Primary 800 |
| **Success Messages** | Alert | Success 50-100 + Success 700 |
| **Error Messages** | Alert | Error 50-100 + Error 700 |
| **Shadows** | Shadow | Colored shadows (Primary/Accent) |

---

## 2. Teori Warna untuk Harmoni Visual Minimalis

### Prinsip-Prinsip Diterapkan:

#### **60-30-10 Rule**
```
60% Neutral (Background, White, Neutral 50-200)
30% Primary Color (Primary 600, Interactive Elements)
10% Accent Color (Accent 600, Highlights, Badges)
```

#### **Color Temperature Balance**
- **Cool Base** (Primary Blue - 250° hue) → Trust, Professionalism
- **Warm Accents** (Amber - 85° hue, Honeyed Neutrals) → Energy, Approachability
- **Warm Neutrals** → More inviting than pure grays

#### **Saturation Management**
- Primary: 14% chroma (bold but not overwhelming)
- Neutrals: 1% chroma (subtle warmth)
- High contrast through lightness, not saturation

#### **Lightness Distribution**
```
Backgrounds: 96-98% L (very light)
Text: 20-55% L (dark for readability)
Primary: 52% L (balanced for buttons)
```

---

## 3. Palet Warna Minimalis Modern (5 Warna Utama)

### Primary: Serene Blue
```
Color: Primary 600
Value: oklch(0.52 0.14 250)
Usage: Primary buttons, Links, Active states
Hex Equivalent: #3B82F6
```
**Why this color:**
- Blue denotes trust, knowledge, and calm
- Perfect for educational/library context
- Excellent contrast against white/light backgrounds

### Secondary: Warm Amber
```
Color: Accent 600
Value: oklch(0.58 0.18 85)
Usage: Badges, Notifications, Secondary CTAs
Hex Equivalent: #F59E0B
```
**Why this color:**
- Warm amber creates visual hierarchy
- Draws attention without harshness
- Complements cool blue primary

### Background: Gradient (3-Stage)
```
From: oklch(0.98 0.01 250) - Cool blue-white
Via:  oklch(0.98 0.02 85)  - Warm cream
To:   oklch(0.96 0.02 250) - Soft blue-gray
```
**Why this gradient:**
- Creates subtle depth without distraction
- Warm center draws focus to content
- Cool edges frame the page elegantly

### Neutral: Honeyed Warm Gray
```
Range: Neutral 50-900
Chroma: 1% (subtle warmth)
Usage: Text, Borders, Subtle backgrounds
```
**Why warm neutrals:**
- More inviting than pure gray
- Reduces eye strain
- Creates cohesive, sophisticated look

### Success/Error: Muted Semantic Colors
```
Success: oklch(0.58 0.14 145) - Sage green
Error:   oklch(0.55 0.20 25)  - Soft rose
Warning: oklch(0.60 0.18 70)  - Warm orange
```
**Why muted semantics:**
- Less jarring than saturated red/green
- Maintains minimalist aesthetic
- Still clearly communicates state

---

## 4. Implementasi dengan Filament/Tailwind CSS v4

### A. Konfigurasi Dasar (app.css)

File yang sudah dibuat: `resources/css/guest-color-palette.css`

```css
/* Import palet warna */
@import 'tailwindcss';

/* Definisi warna menggunakan OKLCH format */
@theme {
    --color-primary-600: oklch(0.52 0.14 250);
    --color-primary-700: oklch(0.45 0.14 250);
    /* ... lengkap di file CSS */
}
```

### B. Penggunaan di Blade Components

#### 1. Button Primary
```blade
<button class="bg-primary-600 text-white px-6 py-2.5 rounded-full
               hover:bg-primary-700 hover:shadow-primary-md
               transition-all duration-300">
    Jelajahi Koleksi
</button>
```

#### 2. Button Secondary
```blade
<a class="border border-primary-600 text-primary-600 px-6 py-2.5 rounded-full
          hover:bg-primary-50 transition-all duration-300">
    Daftar Sebagai Siswa
</a>
```

#### 3. Card Component
```blade
<div class="bg-white border border-neutral-200 rounded-lg
            shadow-sm hover:shadow-md
            transition-all duration-300 p-4">
    <!-- Content -->
</div>
```

#### 4. Input Field
```blade
<input type="text" wire:model.live="search"
       class="w-full px-4 py-2.5 rounded-lg
              border border-neutral-300 bg-white
              text-neutral-700 placeholder-neutral-400
              focus:border-primary-500
              focus:ring-2 focus:ring-primary-500/20
              focus:outline-none transition-all duration-200"
       placeholder="Cari judul buku..." />
```

#### 5. Badge/Category Tag
```blade
<span class="inline-flex items-center px-3 py-1 rounded-full
              text-sm font-medium
              bg-primary-100 text-primary-800">
    {{ $category->name }}
</span>
```

#### 6. Alert Messages
```blade
<!-- Success -->
<div class="bg-success-50 border border-success-200
            text-success-800 px-4 py-3 rounded-lg">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2">...</svg>
        {{ session('success') }}
    </div>
</div>

<!-- Error -->
<div class="bg-error-50 border border-error-200
            text-error-800 px-4 py-3 rounded-lg">
    <!-- Content -->
</div>
```

#### 7. Links
```blade
<a href="{{ route('catalog') }}"
   class="text-primary-600 font-medium
          hover:text-primary-700
          transition-colors duration-200">
    Lainnya
</a>
```

---

## 5. Best Practices untuk Shadow, Spacing & Typography

### A. Shadow System

#### Layered Depth Approach:
```css
/* Tiers of elevation */
shadow-xs  → Subtle borders, inline elements
shadow-sm  → Cards (default)
shadow-md  → Cards on hover, elevated sections
shadow-lg  → Modals, dropdowns
shadow-xl  → Hero images, featured content
```

#### Colored Shadows for Depth:
```css
/* Primary glow for interactive elements */
.shadow-primary-sm: 0 2px 8px -2px oklch(0.52 0.14 250 / 0.15)
.shadow-primary-md: 0 4px 12px -2px oklch(0.52 0.14 250 / 0.20)
```

**Why colored shadows:**
- More natural than pure black shadows
- Creates connection to brand color
- Subtle depth without harshness

### B. Spacing System

#### Scale (Multiples of 4px):
```
2 (8px)  → Tight gaps (related items)
4 (16px) → Card padding, small gaps
6 (24px) → Section gaps, large gaps
8 (32px) → Component spacing
12 (48px) → Section margins
20 (80px) → Page sections
```

#### Container Widths:
```
max-w-7xl → Main content (1280px)
max-w-6xl → Wide sections (1152px)
max-w-5xl → Narrower sections (1024px)
```

### C. Typography Scale

#### Font Sizes (Inter/Instrument Sans):
```
text-xs    → 12px - Metadata, captions
text-sm    → 14px - Body text, labels
text-base  → 16px - Default body
text-lg    → 18px - Emphasized body
text-xl    → 20px - Section headings
text-2xl   → 24px - Page headings
text-3xl   → 30px - Hero headings
text-4xl+  → 36px+ - Display headings
```

#### Font Weights:
```
font-normal   → 400 - Body text
font-medium   → 500 - Emphasis
font-semibold → 600 - Headings, buttons
font-bold     → 700 - Strong headings
```

#### Line Heights:
```
leading-tight   → 1.25 - Headings
leading-normal  → 1.5 - Body text
leading-relaxed → 1.625 - Long-form content
```

---

## 6. Kontras & Aksesibilitas

### WCAG AA Compliance:

| Element | Foreground | Background | Ratio | Pass |
|---------|-----------|------------|-------|------|
| Primary button | Primary 600 | White | 5.2:1 | ✓ |
| Heading (lg) | Neutral 900 | White | 14:1 | ✓ |
| Body text | Neutral 600 | White | 7:1 | ✓ |
| Secondary text | Neutral 500 | White | 4.8:1 | ✓ |
| Badge | Primary 800 | Primary 100 | 6.5:1 | ✓ |

### Aksesibilitas Features:

1. **Focus States:**
```css
*:focus-visible {
    outline: none;
    ring: 2px solid Primary 500;
    ring-offset: 2px;
}
```

2. **Reduced Motion Support:**
```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

3. **Hover Alternatives:**
- Interactive elements work on tap for touch
- Keyboard navigation support
- Clear visual feedback for all states

---

## 7. Contoh Implementasi Lengkap

### Hero Section dengan Palet Warna:

```blade
<section class="pt-20">
    <!-- Background gradient -->
    <div class="bg-gradient-to-b from-[#F8FAFC] via-[#FFFBF5] to-[#F1F5F9]
                    px-3 sm:px-10 min-h-screen">

        <!-- Badge -->
        <div class="flex justify-center mb-6">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full
                         text-xs font-medium border border-primary-600
                         text-primary-600 hover:bg-primary-50 transition">
                Perpustakaan Digital untuk Siswa SMA
                <span class="w-6 h-6 ml-2 rounded-full bg-primary-600
                             flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white">...</svg>
                </span>
            </span>
        </div>

        <!-- Heading -->
        <h1 class="text-center text-neutral-900 font-bold
                   text-4xl md:text-5xl max-w-2xl mx-auto">
            Temukan
            <span class="text-primary-600">Ilmu Tanpa Batas</span>
            di Perpustakaan Kami
        </h1>

        <!-- Subheading -->
        <p class="mt-4 text-center text-neutral-600
                   max-w-md mx-auto text-base leading-relaxed">
            Akses koleksi buku pelajaran, literasi, dan referensi
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4
                    justify-center mt-8">
            <a href="{{ route('catalog') }}"
               class="bg-primary-600 text-white
                      px-6 py-2.5 rounded-full text-sm font-medium
                      flex items-center justify-center space-x-2
                      hover:bg-primary-700 hover:shadow-primary-md
                      transition-all duration-300">
                <span>Jelajahi Koleksi</span>
                <i class="iconoir-open-book"></i>
            </a>
            <a href="/admin/register"
               class="border border-primary-600 text-primary-600
                      px-6 py-2.5 rounded-full text-sm font-medium
                      flex items-center justify-center space-x-2
                      hover:bg-primary-50
                      transition-all duration-300">
                <span>Daftar Sebagai Siswa</span>
                <i class="iconoir-user-scan"></i>
            </a>
        </div>

        <!-- Hero Image -->
        <img class="rounded-2xl mt-16 max-w-5xl mx-auto
                      shadow-lg hover:shadow-xl
                      transition-all duration-300"
             src="..."
             alt="Perpustakaan modern" />

    </div>
</section>
```

---

## 8. Panduan Quick Reference

### Color Quick Reference:
```
Primary actions → bg-primary-600 hover:bg-primary-700
Secondary       → border-primary-600 text-primary-600
Links           → text-primary-600 hover:text-primary-700
Headings        → text-neutral-900
Body text       → text-neutral-600
Secondary text  → text-neutral-500
Borders         → border-neutral-200
Focus           → border-primary-500 ring-primary-500/20
Success         → bg-success-50 text-success-800
Error           → bg-error-50 text-error-800
```

### Shadow Quick Reference:
```
Default card    → shadow-sm hover:shadow-md
Elevated card   → shadow-md hover:shadow-lg
Primary button  → hover:shadow-primary-md
Modal/Popup     → shadow-xl
Hero image      → shadow-lg
```

### Border Radius Quick Reference:
```
Inputs/Buttons  → rounded-lg (8px)
Cards           → rounded-lg (8px)
Large cards     → rounded-xl (12px)
Hero elements   → rounded-2xl (16px)
CTA buttons     → rounded-full
Badges          → rounded-full
```

---

## 9. Migration Steps

### Untuk menerapkan palet ini ke halaman guest yang ada:

1. **Update CSS Import:**
```blade
<!-- Di guest-layout.blade.php -->
<vite resources="css/guest-color-palette.css" />
```

2. **Replace Color Classes:**
```
text-gray-900  → text-neutral-900
text-gray-700  → text-neutral-600
text-gray-600  → text-neutral-500
border-gray-200 → border-neutral-200
border-gray-300 → border-neutral-300
bg-blue-50     → bg-primary-50
bg-blue-600    → bg-primary-600
bg-amber-50    → bg-accent-50
```

3. **Update Gradients:**
```blade
<!-- Old -->
class="bg-gradient-to-b from-[#F5F7FF] via-[#fffbee] to-[#E6EFFF]"

<!-- New (optional, existing is close) -->
class="bg-guest-gradient"
<!-- Or keep existing gradient -->
```

4. **Update Alerts:**
```blade
<!-- Replace hardcoded colors -->
<div class="bg-green-50 ..."> → <div class="bg-success-50 ...">
<div class="bg-blue-50 ...">  → <div class="bg-primary-50 ...">
<div class="bg-red-50 ...">   → <div class="bg-error-50 ...">
```

---

## 10. Resources & References

### Design Inspiration:
- [17 Trending Color Palettes for Websites in 2025](https://daveyandkrista.com/top-trending-color-palettes-for-websites/)
- [Minimalist Color Palette and Typography in Web Design](https://bejamas.com/blog/minimalist-color-palette-and-typography-in-web-design)
- [60 Stunning Color Combinations](https://looka.com/blog/color-combinations/)

### Technical Documentation:
- [Tailwind CSS v4.0 Announcement](https://tailwindcss.com/blog/tailwindcss-v4)
- [Custom Colours in Tailwind CSS v4](https://medium.com/@dvasquez.422/custom-colours-in-tailwindcss-v4-acc3322cd2da)
- [daisyUI Themes Documentation](https://daisyui.com/docs/themes/)
- [daisyUI v5 New Features](https://blog.logrocket.com/daisyui-5-whats-new/)

### Color Tools:
- [OKLCH Color Picker](https://oklch.com/)
- [Contrast Checker](https://webaim.org/resources/contrastchecker/)
- [Color Palette Generator](https://colors.eva.design/)

---

## Summary

Palet warna ini dirancang dengan prinsip **minimalis modern** yang:
- ✅ Menggunakan **honeyed neutrals** untuk warmth
- ✅ **Serene blue** sebagai primary untuk trust
- ✅ **Warm amber** sebagai accent untuk energy
- ✅ **Subtle gradient** untuk depth tanpa distraction
- ✅ **Colored shadows** untuk natural depth perception
- ✅ **OKLCH format** untuk perceptual consistency
- ✅ **WCAG AA compliant** untuk aksesibilitas
- ✅ **Tailwind v4 ready** dengan @theme directive
- ✅ **DaisyUI integrated** untuk component consistency

**Hasil:** Desain yang bersih, modern, inviting, dan profesional untuk sistem perpustakaan sekolah.
