<template>
    <div v-if="isVisible" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" @click="closeModal">×</span>
            <h2 class="modal-title">{{ title }}</h2>
            <div class="report-options">
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Should be tagged as adult content" />
                    Should be tagged as adult content
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Offensive (rude, obscene)" />
                    Offensive (rude, obscene)
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Spam (ads, self-promotion)" />
                    Spam (ads, self-promotion)
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Off topic (trolling)" />
                    Off topic (trolling)
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Copyright (plagiarism, stealing)" />
                    Copyright (plagiarism, stealing)
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Wrong content (illustration, 3D)" />
                    Wrong content (illustration, 3D)
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Spam or abusive messages" />
                    Spam or abusive messages
                </label>
                <div v-if="showDetailInput" class="detail-input">
                    <label for="detailReason">Tell us why to help us better understand:</label>
                    <textarea
                        id="detailReason"
                        v-model="detailReason"
                        placeholder="Enter your detailed reason here..."
                        rows="4"
                    ></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button class="cancel-btn" @click="closeModal">Cancel</button>
                <button class="report-btn" @click="submitReport" :disabled="!selectedReason">Report</button>
            </div>
        </div>
    </div>
</template>

<script>
import { useReportStore } from '../../stores/reportStore';
import { useUserStore } from '../../stores/userStore';
import { notification } from 'ant-design-vue';

export default {
    name: 'ReportModal',
    props: {
        isVisible: {
            type: Boolean,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        photoId: {
            type: Number,
            default: null,
        },
        commentId: {
            type: Number,
            default: null,
        },
        galleryId: {
            type: Number,
            default: null,
        },
        violatorId: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            selectedReason: null,
            detailReason: '',
        };
    },
    computed: {
        reportStore() {
            return useReportStore();
        },
        userStore() {
            return useUserStore();
        },
        showDetailInput() {
            return this.selectedReason === 'Copyright (plagiarism, stealing)' ||
                this.selectedReason === 'Spam or abusive messages';
        }
    },
    methods: {
        closeModal() {
            this.selectedReason = null;
            this.detailReason = '';
            this.$emit('close');
        },
        async submitReport() {
            // Kiểm tra nếu chưa chọn lý do
            if (!this.selectedReason) {
                notification.error({
                    message: 'Error',
                    description: 'Please select a reason for reporting.',
                    placement: 'topRight',
                    duration: 3,
                });
                return;
            }

            // Kiểm tra nếu cần chi tiết nhưng chưa nhập
            if (this.showDetailInput && !this.detailReason.trim()) {
                notification.error({
                    message: 'Error',
                    description: 'Please enter detailed reason.',
                    placement: 'topRight',
                    duration: 3,
                });
                return;
            }

            try {
                // Lấy thông tin người dùng
                await this.userStore.fetchUserData();
                const reporterId = this.userStore.user?.id;

                if (!reporterId) {
                    throw new Error('User information not found.');
                }

                // Xác định giá trị reason
                const reason = this.showDetailInput ? this.detailReason : this.selectedReason;

                // Tạo payload để gửi
                const payload = {
                    reporterId,
                    violatorId: this.violatorId,
                    reason,  // Giá trị reason đã được xác định
                    photoId: this.photoId,
                    commentId: this.commentId,
                    galleryId: this.galleryId,
                };

                // Gửi báo cáo
                await this.reportStore.reportContent(payload);
                this.closeModal();
            } catch (error) {
                // Xử lý lỗi (đã được xử lý trong reportStore)
            }
        }
    }
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: white;
    border-radius: 10px;
    padding: 30px;
    width: 500px;
    max-width: 90%;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    position: relative;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 30px;
    cursor: pointer;
    color: #666;
}

.modal-title {
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 25px;
    text-align: center;
    color: #333;
}

.report-options {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}

.report-option {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    color: #333;
    cursor: pointer;
}

.report-option input[type="radio"] {
    width: 18px;
    height: 18px;
    margin: 0;
}

.detail-input {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-input label {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

.detail-input textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    resize: vertical;
}

.detail-input textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
}

.cancel-btn, .report-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

.cancel-btn {
    background-color: transparent;
    color: #666;
}

.cancel-btn:hover {
    background-color: #f0f0f0;
}

.report-btn {
    background-color: #007bff;
    color: white;
}

.report-btn:hover {
    background-color: #0056b3;
}

.report-btn:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}
</style>
