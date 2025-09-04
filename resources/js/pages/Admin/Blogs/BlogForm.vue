<script setup lang="ts">
import RichTextEditor from '@/components/RichTextEditor.vue';
import SeoForm from '@/components/SeoForm.vue';
import ImagePicker from '@/components/filemanager/ImagePicker.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
// Using a simple textarea for content to avoid extra dependencies

const props = defineProps<{
    blog?: any;
    categories: Array<{ id: number; title: string }>;
    onSaved?: (payload: any) => void;
}>();

const emit = defineEmits<{ (e: 'create'): void; (e: 'update'): void }>();

type BlogFormPayload = {
    title: string;
    slug: string;
    status: number;
    content: string;
    blog_category_id: number | null;
    author: string;
    publish_date: string | null;
    tags: string[];
    image: string | undefined;
    seo: {
        seo_title: string;
        seo_index: number | boolean;
        seo_keyword: string;
        seo_description: string;
        seo_image: string | undefined;
        canonical_url: string;
        meta_json: Record<string, any>;
    };
};

const form = useForm<BlogFormPayload>({
    title: props.blog?.title ?? '',
    slug: props.blog?.slug ?? '',
    status: props.blog?.status ?? 1,
    content: props.blog?.content ?? '',
    blog_category_id: props.blog?.blog_category_id ?? props.blog?.category?.id ?? null,
    author: props.blog?.author ?? '',
    publish_date: props.blog?.publish_date
        ? new Date(props.blog.publish_date).toISOString().slice(0, 16)
        : (() => {
              const now = new Date();
              now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
              return now.toISOString().slice(0, 16);
          })(),
    tags: Array.isArray(props.blog?.tags) ? props.blog?.tags : [],
    image: props.blog?.image ?? undefined,
    seo: {
        seo_title: props.blog?.seo?.seo_title ?? '',
        seo_index: props.blog?.seo?.seo_index ?? 1,
        seo_keyword: props.blog?.seo?.seo_keyword ?? '',
        seo_description: props.blog?.seo?.seo_description ?? '',
        seo_image: props.blog?.seo?.seo_image ?? undefined,
        canonical_url: props.blog?.seo?.canonical_url ?? '',
        meta_json: props.blog?.seo?.meta_json ?? {},
    },
});

// Keep complex `template` data outside of Inertia form payload to satisfy type constraints
const template = ref<Record<string, any> | null>(props.blog?.template ?? null);

form.transform((data: any) => ({
    ...data,
    // Inject template back into the submitted payload
    template: template.value ?? null,
    blog_category_id: data.blog_category_id ? Number(data.blog_category_id) : null,
    tags: (data.tags || []).filter((t: any) => !!t && String(t).trim() !== ''),
    seo: {
        ...data.seo,
        seo_index: data.seo?.seo_index === true ? 1 : data.seo?.seo_index === false ? 0 : Number(data.seo?.seo_index ?? 1),
    },
}));

const submitting = ref(false);
const hasErrors = computed(() => Object.keys((form.errors as unknown as Record<string, any>) || {}).length > 0);

// Slug auto-generation with manual override
const slugEditedManually = ref(false);
function slugify(input: string): string {
    return (input || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[^\w\s-]/g, '')
        .trim()
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}
watch(
    () => form.title,
    (v) => {
        if (!slugEditedManually.value) {
            form.slug = slugify(v || '');
        }
    },
);
function onSlugInput() {
    slugEditedManually.value = true;
}
function regenerateSlug() {
    form.slug = slugify(form.title);
    slugEditedManually.value = false;
}

function submitCreate() {
    submitting.value = true;
    form.post(route('admin.blogs.store'), {
        onFinish: () => (submitting.value = false),
        onSuccess: (payload) => {
            props.onSaved?.(payload);
            emit('create');
        },
        preserveScroll: true,
    });
}

function submitUpdate(id: number) {
    submitting.value = true;
    form.put(route('admin.blogs.update', id), {
        onFinish: () => (submitting.value = false),
        onSuccess: () => emit('update'),
        preserveScroll: true,
    });
}

// Content is a plain textarea; no special handling needed beyond v-model

// Tag management
const newTag = ref('');

function addTag() {
    const tagToAdd = newTag.value.trim();
    if (tagToAdd && !form.tags.includes(tagToAdd)) {
        form.tags.push(tagToAdd);
    }
    newTag.value = '';
}

function removeTag(index: number) {
    form.tags.splice(index, 1);
}

const blogPlaceholder = computed(() => (props.blog?.placeholder as string | undefined) ?? '/storage/placeholder.png');

const seoPlaceholder = computed(() => (props.blog?.seo?.placeholder as string | undefined) ?? '/storage/placeholder.png');
</script>

<template>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main -->
        <div class="space-y-6 lg:col-span-2">
            <div v-if="hasErrors" class="rounded border border-red-200 bg-red-50 p-3 text-red-700">
                Please fix the highlighted fields and try again.
            </div>
            <div class="space-y-4 rounded border bg-white p-4">
                <div>
                    <label class="block text-sm font-medium">Title</label>
                    <input v-model="form.title" type="text" class="mt-1 w-full rounded border px-3 py-2" />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium">Slug</label>
                    <div class="flex items-center gap-2">
                        <input v-model="form.slug" @input="onSlugInput" type="text" class="mt-1 w-full rounded border px-3 py-2" />
                        <button type="button" class="mt-1 rounded border px-3 py-2" @click="regenerateSlug">Auto</button>
                    </div>
                    <div v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">Author</label>
                        <input v-model="form.author" type="text" class="mt-1 w-full rounded border px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Publish Date</label>
                        <input v-model="form.publish_date" type="datetime-local" class="mt-1 w-full rounded border px-3 py-2" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Featured Image</label>
                    <ImagePicker :placeholder="blogPlaceholder" v-model="form.image" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Content</label>
                    <!-- <RichTextEditor v-model="form.content" /> -->
                    <textarea v-model="form.content" class="mt-1 w-full rounded border px-3 py-2" rows="8"></textarea>
                    <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</div>
                </div>
            </div>

            <SeoForm v-model="form.seo" :title="form.title" :slug="form.slug" :host="route('home') + '/blog'" :placeholder="seoPlaceholder" />
        </div>

        <!-- Side -->
        <div class="space-y-6">
            <div class="space-y-3 rounded border bg-white p-4">
                <div>
                    <label class="block text-sm font-medium">Status</label>
                    <select v-model.number="form.status" class="mt-1 w-full rounded border px-3 py-2">
                        <option :value="1">Publish</option>
                        <option :value="0">Draft</option>
                    </select>
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-medium">Category</label>
                    <select v-model.number="form.blog_category_id" class="mt-1 w-full rounded border px-3 py-2">
                        <option :value="null">— Select —</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.title }}</option>
                    </select>
                    <div v-if="form.errors.blog_category_id" class="mt-1 text-sm text-red-600">{{ form.errors.blog_category_id }}</div>
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-medium">Tags</label>
                    <div class="mt-1">
                        <input
                            v-model="newTag"
                            @keydown.enter.prevent="addTag"
                            type="text"
                            class="w-full rounded border px-3 py-2"
                            placeholder="Enter tag and press Enter"
                        />
                    </div>
                    <div v-if="form.tags.length" class="mt-2 flex flex-wrap gap-2">
                        <span
                            v-for="(tag, index) in form.tags"
                            :key="index"
                            class="flex items-center rounded-md bg-blue-400 px-2 py-1 text-sm font-medium text-white"
                        >
                            {{ tag }}
                            <button @click="removeTag(index)" class="text-md ml-2 leading-none text-white hover:text-gray-200">&times;</button>
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <button v-if="!props.blog" :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitCreate">
                        Create
                    </button>
                    <button v-else :disabled="submitting" class="w-full rounded bg-primary px-4 py-2 text-white" @click="submitUpdate(props.blog.id)">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
