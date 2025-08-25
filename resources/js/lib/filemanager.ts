import axios from 'axios'

export type FmDirectory = { name: string; path: string }
export type FmImage = { name: string; path: string; thumb: string | null; href: string }
export type FmListResponse = {
  directories: FmDirectory[]
  images: FmImage[]
  pagination: { total: number; page: number; limit: number }
  directory: string
  filter_name: string
}

const base = '/fm'

export const fmApi = {
  async list(params: { directory?: string; filter_name?: string; page?: number; limit?: number } = {}) {
    const { data } = await axios.get<FmListResponse>(`${base}/list`, { params })
    return data
  },
  async upload(files: File[], directory?: string) {
    const fd = new FormData()
    files.forEach((f) => fd.append('file[]', f))
    const { data } = await axios.post(`${base}/upload`, fd, { params: { directory } })
    return data as { success?: string; error?: string }
  },
  async createFolder(folder: string, directory?: string) {
    const { data } = await axios.post(`${base}/folder`, { folder }, { params: { directory } })
    return data as { success?: string; error?: string }
  },
  async delete(paths: string[]) {
    const fd = new URLSearchParams()
    paths.forEach((p) => fd.append('path[]', p))
    const { data } = await axios.post(`${base}/delete`, fd)
    return data as { success?: string; error?: string }
  },
}
