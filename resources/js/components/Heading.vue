<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
    /**
     * Tailwind margin-bottom class for the container. Defaults to 'mb-8'.
     */
    marginClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    marginClass: 'mb-8',
});
</script>

<template>
    <div :class="[props.marginClass, 'space-y-0.5']">
        <h2 class="text-xl font-semibold tracking-tight">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </div>
</template>
