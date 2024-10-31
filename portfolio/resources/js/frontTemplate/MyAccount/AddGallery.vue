<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="gallery-header">
                <button class="back-button" @click="cancel">←</button>
                <span class="title-header">Create Gallery</span>
            </div>
            <div class="add-gallery-container">
                <form @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="title">Title*</label>
                        <input type="text" id="title" v-model="title" required />
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" v-model="description"></textarea>
                    </div>
                    <div class="form-group visibility-group">
                        <label>Visibility</label>
                        <div class="visibility-options">
                            <div class="visibility-option">
                                <input type="radio" id="public" value="public" v-model="visibility" />
                                <label for="public">Visible to everyone</label>
                            </div>
                            <div class="visibility-option">
                                <input type="radio" id="private" value="private" v-model="visibility" />
                                <label for="private">Only visible to me</label>
                            </div>
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="button" class="btn-cancel" @click="cancel">Cancel</button>
                        <button type="submit" class="btn-create">Create</button>
                    </div>
                </form>
            </div>
        </template>
    </Layout>
</template>

<script>
import Layout from '../Layout.vue';

export default {
    name: 'AddGallery',
    components: {
        Layout,
    },
    data() {
        return {
            title: '',
            description: '',
            visibility: 'public',
        };
    },
    methods: {
        handleSubmit() {
            console.log('Gallery Created:', {
                title: this.title,
                description: this.description,
                visibility: this.visibility,
            });
        },
        cancel() {
            this.$router.push('/'); // Redirect to the desired page
        },
    },
};
</script>

<style scoped>
.gallery-header {
    display: flex;
    align-items: center;
    background-color: white;
    padding: 10px 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.back-button {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    margin-top: 90px;
}

.title-header {
    flex-grow: 1;
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    margin-top: 90px;
}

.add-gallery-container {
    max-width: 600px;
    margin: 20px auto; /* Khoảng cách từ bên ngoài */
    padding: 40px; /* Khoảng cách bên trong */
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"],
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

textarea {
    height: 100px;
}

.visibility-group {
    margin-bottom: 15px;
}

.visibility-options {
    display: flex; /* Đặt chế độ hiển thị thành flex */
    flex-direction: row; /* Xếp theo hàng ngang */
    gap: 20px; /* Khoảng cách giữa các tùy chọn */
}

.visibility-option {
    display: flex;
    align-items: center; /* Căn giữa radio và label */
}

.visibility-option input[type="radio"] {
    margin-right: 5px; /* Khoảng cách giữa radio và label */
}

.visibility-option label {
    margin-left: 5px; /* Khoảng cách bên trái cho label */
}

.button-group {
    display: flex;
    justify-content: flex-end; /* Căn phải cho các nút */
    margin-top: 20px; /* Khoảng cách trên cùng */
}

.btn-create {
    padding: 12px 24px; /* Kích thước nút */
    border: none;
    border-radius: 20px; /* Bo góc tròn */
    cursor: pointer;
    font-size: 16px; /* Kích thước font chữ */
    font-weight: bold; /* Đậm chữ */
    background-color: #007bff; /* Nền xanh cho nút Create */
    color: white; /* Màu chữ trắng */
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.5); /* Hiệu ứng bóng */
    transition: background-color 0.3s, transform 0.2s; /* Hiệu ứng chuyển màu và phóng to */
}

.btn-cancel {
    padding: 12px 24px; /* Kích thước nút */
    border: none; /* Xóa viền */
    border-radius: 20px; /* Bo góc tròn */
    cursor: pointer;
    font-size: 16px; /* Kích thước font chữ */
    margin-left: 10px; /* Khoảng cách giữa các nút */
    background-color: transparent; /* Nền trong suốt cho nút Cancel */
    color: #007bff; /* Màu chữ xanh */
    transition: background-color 0.3s; /* Hiệu ứng chuyển màu */
}

/* Hiệu ứng hover */
.btn-create:hover {
    background-color: #0056b3; /* Màu xanh đậm khi hover */
}

.btn-cancel:hover {
    background-color: rgba(0, 123, 255, 0.1); /* Nền sáng lên khi hover */
}
</style>
