<template>
    <div v-if="isVisible" class="modal-overlay">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <span class="close" @click="closeModal">
                    <i class="fa-solid fa-xmark"></i>
                </span>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="cover-image">
                    <div class="image-preview">
                        <img :src="tempUser.cover_photo || '/front_assets/img/img_5.jpg'" alt="Cover Preview" />
                    </div>
                    <label class="edit-label" @click="selectCoverImage">
                        <i class="fa-solid fa-pencil-alt"></i>
                    </label>
                </div>

                <div class="profile-image" style="margin-top: -70px;">
                    <div class="image-preview">
                        <img :src="tempUser.profile_picture || '/front_assets/img/img_5.jpg'" alt="Profile Preview" />
                    </div>
                    <label style="margin-right: 150px; margin-top: 17px" class="edit-label" @click="selectProfileImage">
                        <i class="fa-solid fa-pencil-alt"></i>
                    </label>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" v-model="tempUser.username" placeholder="Enter your username" maxlength="50" />
                    <small class="char-count">{{ tempUser.username.length }}/50</small>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" v-model="tempUser.email" placeholder="Enter your email" maxlength="150" />
                    <small class="char-count">{{ tempUser.email.length }}/150</small>
                </div>

                <div class="form-group">
                    <label for="bio">Bio:</label>
                    <textarea id="bio" v-model="tempUser.bio" placeholder="Tell us about yourself" maxlength="255"></textarea>
                    <small class="char-count">{{ tempUser.bio.length }}/255</small>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button class="cancel-btn" @click="closeModal">Cancel</button>
                <button class="save-btn" @click="saveChanges">Save</button>
            </div>
        </div>

        <!-- Hidden file inputs for cover and profile images -->
        <input type="file" ref="coverImageInput" accept="image/*" @change="onCoverImageChange" style="display:none;" />
        <input type="file" ref="profileImageInput" accept="image/*" @change="onProfileImageChange" style="display:none;" />
    </div>
</template>

<script>
import { notification } from 'ant-design-vue';
import { useUserStore } from '../../../stores/userStore.js';
import { storeToRefs } from 'pinia';

export default {
    props: {
        isVisible: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        const userStore = useUserStore();
        const { user } = storeToRefs(userStore);
        return {
            userStore,
            user,
            tempUser: { ...user.value },
            coverFile: null,
            profileFile: null,
        };
    },
    methods: {
        closeModal() {
            this.$emit("close");
        },
        selectCoverImage() {
            this.$refs.coverImageInput.click();
        },
        selectProfileImage() {
            this.$refs.profileImageInput.click();
        },
        onCoverImageChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.coverFile = file;
                this.tempUser.cover_photo = URL.createObjectURL(file);
            }
        },
        onProfileImageChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.profileFile = file;
                this.tempUser.profile_picture = URL.createObjectURL(file);
            }
        },
        async saveChanges() {
            try {
                await this.userStore.updateUserProfile(this.tempUser, this.profileFile, this.coverFile);
                notification.success({
                    message: 'Success',
                    description: 'Profile updated successfully!',
                });
                this.closeModal();
            } catch (error) {
                notification.error({
                    message: 'Error',
                    description: 'Failed to update profile!',
                });
            }
        },
    },
    mounted() {
        this.userStore.fetchUserData();
    },
    watch: {
        isVisible(newVal) {
            if (newVal) {
                this.userStore.fetchUserData();
                this.tempUser = { ...this.user };
            }
        }
    }
};
</script>

<style scoped>
/* Overlay to dim the background */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: flex;
    justify-content: flex-end;
}

/* Modal content */
.modal-content {
    background-color: white;
    width: 500px;
    height: 100%; /* Full height */
    padding: 20px;
    box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.cover-image {
    position: relative;
    margin-bottom: 20px;
}

.cover-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

.edit-label {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgb(8, 112, 209);
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
}

.profile-image {
    position: relative;
    margin-bottom: 20px;
}

.profile-image img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.image-preview {
    margin: 10px 0;
    text-align: center;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close {
    cursor: pointer;
    font-size: 28px;
}

.content {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 20px;
}

.form-group {
    margin: 15px 0;
}

.char-count {
    font-size: 12px;
    color: gray;
    margin-top: 5px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.cancel-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    margin-left: 10px;
    background-color: transparent;
    color: #007bff;
    transition: background-color 0.3s;
}

.save-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    background-color: #007bff;
    color: white;
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.5);
    transition: background-color 0.3s, transform 0.2s;
}

.save-btn:hover {
    background-color: #0056b3;
}

.cancel-btn:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

input,
textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

textarea {
    resize: vertical;
}
</style>
