<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{ model: Record<string, any> }>()

const openIndex = ref<number | null>(null)
function toggle(i: number) {
  openIndex.value = openIndex.value === i ? null : i
}

// Transition helpers copied to match pages/FAQ.vue behavior
function beforeEnter(el: Element) {
  const e = el as HTMLElement
  e.style.height = '0px'
  e.style.opacity = '0'
}
function onEnter(el: Element, done: () => void) {
  const e = el as HTMLElement
  e.style.overflow = 'hidden'
  const h = e.scrollHeight
  // Force reflow
  // eslint-disable-next-line @typescript-eslint/no-unused-expressions
  e.offsetHeight
  e.style.transition = 'height 180ms cubic-bezier(0.22, 1, 0.36, 1), opacity 160ms ease'
  e.style.height = h + 'px'
  e.style.opacity = '1'
  const cleanup = () => {
    e.style.transition = ''
    e.style.height = ''
    e.style.overflow = ''
    e.removeEventListener('transitionend', onEnd)
    done()
  }
  const onEnd = (evt: TransitionEvent) => {
    if (evt.propertyName === 'height') cleanup()
  }
  e.addEventListener('transitionend', onEnd)
}
function beforeLeave(el: Element) {
  const e = el as HTMLElement
  e.style.height = e.scrollHeight + 'px'
  e.style.opacity = '1'
}
function onLeave(el: Element, done: () => void) {
  const e = el as HTMLElement
  e.style.overflow = 'hidden'
  // Force reflow
  // eslint-disable-next-line @typescript-eslint/no-unused-expressions
  e.offsetHeight
  e.style.transition = 'height 180ms cubic-bezier(0.22, 1, 0.36, 1), opacity 160ms ease'
  e.style.height = '0px'
  e.style.opacity = '0'
  const cleanup = () => {
    e.style.transition = ''
    e.style.overflow = ''
    e.removeEventListener('transitionend', onEnd)
    done()
  }
  const onEnd = (evt: TransitionEvent) => {
    if (evt.propertyName === 'height') cleanup()
  }
  e.addEventListener('transitionend', onEnd)
}
</script>

<template>
  <section class="py-12 md:py-16 px-6 md:px-12 max-w-6xl mx-auto">
    <div class="text-center">
      <h2 class="text-2xl md:text-3xl font-semibold">{{ props.model.title || '' }}</h2>
      <p class="mt-2 text-foreground/70">{{ props.model.desc || '' }}</p>
    </div>

    <div class="mt-8 md:mt-10 grid md:grid-cols-2 gap-4 md:gap-6">
      <div
        v-for="(it, i) in (props.model.items || [])"
        :key="i"
        class="rounded-xl border bg-background/50"
      >
        <button
          type="button"
          class="w-full text-left px-4 md:px-5 py-4 md:py-5 flex items-start gap-3 hover:bg-muted/30 rounded-t-xl"
          @click="toggle(i)"
        >
          <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary ring-1 ring-primary/20">
            <svg v-if="openIndex === i" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.75 4.75a.75.75 0 00-1.5 0v10.5a.75.75 0 001.5 0V4.75zM4.75 9.25a.75.75 0 000 1.5h10.5a.75.75 0 000-1.5H4.75z" clip-rule="evenodd"/></svg>
          </span>
          <span class="font-medium">{{ it.question }}</span>
        </button>
        <Transition :css="false" @before-enter="beforeEnter" @enter="onEnter" @before-leave="beforeLeave" @leave="onLeave">
          <div v-if="openIndex === i" class="px-4 md:px-5 pb-4 md:pb-5 text-sm text-foreground/80">
            <div class="prose" v-html="it.answer"></div>
          </div>
        </Transition>
      </div>
    </div>
  </section>
</template>
