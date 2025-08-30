export function decodeAndStrip(label: string): string {
  if (!label) return ''
  const stripped = label.replace(/<[^>]*>/g, '')
  const textarea = document.createElement('textarea')
  textarea.innerHTML = stripped
  return textarea.value
}
