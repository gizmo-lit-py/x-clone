<script setup>
import { ref, onMounted } from 'vue'

const posts = ref([])
const text = ref('')
const errorMessage = ref('')
const editingId = ref(null)
const editingText = ref('')

const fetchData = async () => {
  const response = await fetch('http://localhost:8000/posts.php')

  const data = await response.json()

  posts.value = data
}

const createPost = async () => {
  const response = await fetch('http://localhost:8000/posts.php', {
    method: 'POST',

    headers: {
      'Content-Type': 'application/json'
    },

    body: JSON.stringify({
      body: text.value
    })
  })

  const data = await response.json()

  if (!response.ok) {
    errorMessage.value = data.error
    return
  }

    errorMessage.value = ''
    text.value = ''

    await fetchData()
  }


onMounted(() => {
  fetchData()
})

const deletePost = async (postId) => {
  const response = await fetch('http://localhost:8000/posts.php', {
    method: 'DELETE',

    headers: {
      'Content-Type': 'application/json'
    },

    body: JSON.stringify({
      id: postId
    })
  })

  const data = await response.json()

  if (!response.ok) {
    errorMessage.value = data.error
    return
  }

  errorMessage.value = ''

  await fetchData()
}

const startEdit = (post) => {
  editingId.value = post.id
  editingText.value = post.body

}

const updatePost = async (postId) => {
  const response = await fetch('http://localhost:8000/posts.php', {
    method: 'PATCH',

    headers: {
      'Content-Type' : 'application/json'
    },

    body: JSON.stringify({
      id: postId,
      body: editingText.value
    })
  })

  const data = await response.json()

  if (!response.ok) {
    errorMessage.value = data.error
    return
  }

  errorMessage.value = ''

  editingId.value = null
  editingText.value = ''

  await fetchData()

}

</script>

<template>
  <textarea
    v-model="text"
    placeholder="今どうしてる？"
  ></textarea>

  <p v-if="errorMessage">
      {{ errorMessage}}
  </p>
  <div>
    <button @click="createPost">
      投稿する
    </button>
  </div>

  <div
    v-for="post in posts"
    :key="post.id"
  >
    <p>{{ post.id }}</p>
    <p>{{ post.username }}</p>
    <p v-if="editingId !== post.id">
      {{ post.body }}
    </p>

    <textarea
      v-else
      v-model="editingText"
      ></textarea>
    <p>{{ post.created_at }}</p>

    <button @click="deletePost(post.id)">
      削除
    </button>

    <button @click="startEdit(post)">
      編集
    </button>

    <button
      v-if="editingId === post.id"
      @click="updatePost(post.id)">
      保存
    </button>
  </div>
</template>

<style scoped></style>