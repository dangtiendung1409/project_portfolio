<template>
    <div v-if="isVisible" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" @click="closeModal">×</span>
            <h2 class="modal-title">Report Comment</h2>
            <div class="report-options">
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Harassment or hate speech" />
                    Harassment or hate speech
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Spam or misleading" />
                    Spam or misleading
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Violence or harmful behavior" />
                    Violence or harmful behavior
                </label>
                <label class="report-option">
                    <input type="radio" v-model="selectedReason" value="Other" />
                    Other
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
    name: 'ReportCommentModal',
    props: {
        isVisible: {
            type: Boolean,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        commentId: {
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
            return this.selectedReason === 'Other';
        }
    },
    methods: {
        closeModal() {
            this.selectedReason = null;
            this.detailReason = '';
            this.$emit('close');
        },
        async submitReport() {
            if (!this.selectedReason) {
                notification.error({
                    message: 'Error',
                    description: 'Please select a reason for reporting.',
                    placement: 'topRight',
                    duration: 3,
                });
                return;
            }

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
                await this.userStore.fetchUserData();
                const reporterId = this.userStore.user?.id;

                if (!reporterId) {
                    throw new Error('User information not found.');
                }

                const reason = this.showDetailInput ? this.detailReason : this.selectedReason;

                const payload = {
                    reporterId,
                    violatorId: this.violatorId,
                    reason,
                    commentId: this.commentId,
                };

                await this.reportStore.reportContent(payload);
                this.closeModal();
            } catch (error) {
                // Handle error (already handled in reportStore)
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
