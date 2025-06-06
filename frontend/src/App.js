import React, { useState, useEffect } from 'react';

function App() {
  const [username, setUsername] = useState('');
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');
  const [posts, setPosts] = useState([]);

  const registerUser = async () => {
    const res = await fetch('http://localhost:8000/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username: username,
        password: '123'
      })
    });
    alert(await res.text());
  };

  const createPost = async () => {
    await fetch('http://localhost:8080/create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ title, content })
    });
    fetchPosts();
  };

  const fetchPosts = async () => {
    const res = await fetch('http://localhost:8080/');
    setPosts(await res.json());
  };

  useEffect(() => {
    fetchPosts();
  }, []);

  console.log("posts",posts)
  return (
    <div>
      <h1>🐶 Hello Cun Con</h1>
      <div>
        <input value={username} onChange={e => setUsername(e.target.value)} placeholder="Username" />
        <button onClick={registerUser}>Đăng ký</button>
      </div>
      <div>
        <input value={title} onChange={e => setTitle(e.target.value)} placeholder="Tiêu đề" />
        <textarea value={content} onChange={e => setContent(e.target.value)} placeholder="Nội dung"></textarea>
        <button onClick={createPost}>Tạo bài viết</button>
      </div>
      <h2>Danh sách bài viết:</h2>
      <ul>
        {posts.map((p, idx) => (
          <li key={idx}><strong>{p.title}</strong>: {p.content}</li>
        ))}
      </ul>
    </div>
  );
}

export default App;
// import React, { useState, useEffect } from 'react';

// function App() {
//   const [username, setUsername] = useState('');
//   const [title, setTitle] = useState('');
//   const [content, setContent] = useState('');
//   const [posts, setPosts] = useState([]);

//   const registerUser = async () => {
//     const res = await fetch('http://localhost/api/user/register', {
//       method: 'POST',
//       headers: { 'Content-Type': 'application/json' },
//       body: JSON.stringify({
//         username: username,
//         password: '123'
//       })
//     });
//     alert(await res.text());
//   };

//   const createPost = async () => {
//     await fetch('http://localhost/api/posts/create', {
//       method: 'POST',
//       headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//       body: new URLSearchParams({ title, content })
//     });
//     fetchPosts();
//   };

//   const fetchPosts = async () => {
//     const res = await fetch('http://localhost/api/posts/');
//     setPosts(await res.json());
//   };

//   useEffect(() => {
//     fetchPosts();
//   }, []);

//   console.log("posts",posts)
//   return (
//     <div>
//       <h1>🐶 Hello Cun Con</h1>
//       <div>
//         <input value={username} onChange={e => setUsername(e.target.value)} placeholder="Username" />
//         <button onClick={registerUser}>Đăng ký</button>
//       </div>
//       <div>
//         <input value={title} onChange={e => setTitle(e.target.value)} placeholder="Tiêu đề" />
//         <textarea value={content} onChange={e => setContent(e.target.value)} placeholder="Nội dung"></textarea>
//         <button onClick={createPost}>Tạo bài viết</button>
//       </div>
//       <h2>Danh sách bài viết:</h2>
//       <ul>
//         {posts.map((p, idx) => (
//           <li key={idx}><strong>{p.title}</strong>: {p.content}</li>
//         ))}
//       </ul>
//     </div>
//   );
// }

// export default App;
