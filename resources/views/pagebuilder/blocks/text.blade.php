@php($class = trim(($model['class'] ?? '') . ' ' . ($model['padding'] ?? '')))
<section class="{{ $class }}">
  {!! $model['content'] ?? '' !!}
</section>
