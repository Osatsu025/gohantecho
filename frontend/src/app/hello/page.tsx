async function getHello() {
  const res = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL}/hello`);

  if (!res.ok) {
    const errorBody = await res.text();
    throw new Error(`Failed to fetch hello message: ${res.status} ${res.statusText}. Body: ${errorBody}`);
  }

  return res.json();
}

export default async function HelloPage() {
  const helloMessage = await getHello();

  return (
    <main style={{ padding: '2rem' }}>
      <h1>猫ちゃんからのご挨拶</h1>
      <p>{helloMessage.message}</p>
    </main>
  )
}