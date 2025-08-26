<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

// Build FAQs from existing site content (Home, Features, About)
// Keep it concise; design: minimal, clean, 2-column on desktop, smooth toggles.

type FaqItem = { q: string; a: string }

const faqs = ref<FaqItem[]>([
  {
    q: 'What does InsureVerifyAI do?',
    a: 'InsureVerifyAI provides a fast, secure API to verify renter driver licenses and insurance in seconds, reducing fraud and speeding up approvals.'
  },
  {
    q: 'How fast is the verification?',
    a: 'Verifications are near-instant. Our AI and biometric checks process IDs and insurance details in seconds.'
  },
  {
    q: 'How do you verify identity and licenses?',
    a: 'We combine government ID scanning with biometric matching. Our tech stack mirrors the rigor trusted by CLEAR partners.'
  },
  {
    q: 'Can you verify insurance coverage in real-time?',
    a: 'Yes. We read the declarations page and validate that the policy is active and applicable to the rental vehicle.'
  },
  {
    q: 'Is there an option to record the insurance confirmation?',
    a: 'Yes, as an add-on. You receive a recording as proof of coverage, which is helpful if you ever need to file a claim.'
  },
  {
    q: 'Is my data secure?',
    a: 'Yes. We encrypt data in transit and at rest and follow least-privilege principles. Sensitive data is not stored longer than necessary.'
  },
  {
    q: 'Will this scale with my fleet size?',
    a: 'Absolutely. Our infrastructure scales from small rental businesses to enterprise fleets without extra overhead.'
  },
  {
    q: 'How do I get started?',
    a: 'Request API access via Sign Up. You can explore pricing plans and start integrating with our developer-friendly APIs.'
  }
])

const openIndex = ref<number | null>(0)

function toggle(i: number) {
  openIndex.value = openIndex.value === i ? null : i
}

function beforeEnter(el: Element) {
  const e = el as HTMLElement
  e.style.height = '0px'
  e.style.opacity = '0'
  e.style.overflow = 'hidden'
}

function onEnter(el: Element, done: () => void) {
  const e = el as HTMLElement
  const target = e.scrollHeight
  // Force reflow so the transition picks up from 0
  // eslint-disable-next-line @typescript-eslint/no-unused-expressions
  e.offsetHeight
  e.style.transition = 'height 220ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease'
  e.style.height = target + 'px'
  e.style.opacity = '1'
  const cleanup = () => {
    e.style.height = 'auto'
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

function beforeLeave(el: Element) {
  const e = el as HTMLElement
  e.style.overflow = 'hidden'
  e.style.height = e.scrollHeight + 'px'
  e.style.opacity = '1'
}

function onLeave(el: Element, done: () => void) {
  const e = el as HTMLElement
  // Force reflow to lock the start height
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
  <Head title="FAQ" />
  <SiteLayout>
    <section class="py-16 px-6 md:px-12 max-w-6xl mx-auto">
      <div class="text-center">
        <h1 class="text-3xl md:text-4xl font-bold">Frequently Asked Questions</h1>
        <p class="text-foreground/70 mt-2">Quick answers about our verification platform</p>
      </div>

      <div class="mt-10 grid md:grid-cols-2 gap-4 md:gap-6">
        <div v-for="(item, i) in faqs" :key="i" class="rounded-xl border bg-background/50">
          <button type="button" class="w-full text-left px-4 md:px-5 py-4 md:py-5 flex items-start gap-3 hover:bg-muted/30 rounded-t-xl" @click="toggle(i)">
            <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary ring-1 ring-primary/20">
              <svg v-if="openIndex === i" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.75 4.75a.75.75 0 00-1.5 0v10.5a.75.75 0 001.5 0V4.75zM4.75 9.25a.75.75 0 000 1.5h10.5a.75.75 0 000-1.5H4.75z" clip-rule="evenodd"/></svg>
            </span>
            <span class="font-medium">{{ item.q }}</span>
          </button>
          <Transition :css="false" @before-enter="beforeEnter" @enter="onEnter" @before-leave="beforeLeave" @leave="onLeave">
            <div v-if="openIndex === i" class="px-4 md:px-5 pb-4 md:pb-5 text-sm text-foreground/80">
              {{ item.a }}
            </div>
          </Transition>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
