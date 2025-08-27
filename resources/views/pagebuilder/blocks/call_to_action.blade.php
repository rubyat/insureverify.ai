@php(
  $title = $model['title'] ?? ''
)
@php(
  $sub = $model['sub_title'] ?? ''
)
@php(
  $btn = $model['link_title'] ?? 'Learn more'
)
@php(
  $href = $model['link_more'] ?? '#'
)
@php(
  $bg = $model['bg_color'] ?? '#f7fafc'
)
<section class="cta" style="padding:2rem; background: {{ e($bg) }};">
  <div class="container mx-auto text-center">
    <h2 class="text-2xl font-semibold">{{ $title }}</h2>
    <p class="mt-2 opacity-80">{{ $sub }}</p>
    <a href="{{ $href }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded">{{ $btn }}</a>
  </div>
</section>
