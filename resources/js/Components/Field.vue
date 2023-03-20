<template>
    <div v-if="field.type.name === 'select'">
        <el-select
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            @change="send"
        >
            <el-option
                v-for="(option, index) in field.options"
                :key="index"
                :label="option.value"
                :value="option.value"
            />
        </el-select>
    </div>

    <div v-else-if="field.type.name === 'date'">
        <el-input
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            type="date"
            @change="send"
        />
    </div>

    <div v-else-if="field.type.name === 'number'">
        <el-input-number
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            @change="send"
        />
    </div>

    <div v-else-if="field.type.name === 'checkbox'">
        <el-checkbox
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            :label="field.name"
            @change="send"
        />
    </div>

    <div v-else-if="field.type.name === 'email'">
        <el-input
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            type="email"
            :label="field.name"
            :class="error ? 'error' : ''"
            @change="send"
            @input="check"
            @focus="check"
        />
    </div>
    <div v-else-if="field.type.name === 'phone'">
        <el-input
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            :label="field.name"
            :class="error ? 'error' : ''"
            @change="send"
            @input="check"
            @focus="check"
        >
            <template #append v-if="isSenderPrefix">
                <div class="phone-action row common-gap">
                    <i class="la la-comment" @click="popupVisible = true"></i>
                    <i class="la la-phone" @click="call"></i>
                </div>
            </template>
        </el-input>
    </div>
    <div v-else-if="field.type.name === 'address'">
        <el-select
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            filterable
            remote
            allow-create
            reserve-keyword
            @change="send"
            :remote-method="findAddress"
            @focus="check"
        >
            <el-option
                v-for="(option, index) in addresses"
                :key="index"
                :label="option.value"
                :value="option.value"
            />
        </el-select>
    </div>



<!--    Смс-->
    <el-dialog v-model="popupVisible" :title="'Отправить смс на номер ' + field.pivot.value">
        <div>
            <el-checkbox
                v-for="integration in integrations"
                v-model="integration.value"
                :label="integration.title"
            />
        </div>
        <el-input
            v-model="message"
            rows="5"
            type="textarea"
        />
        <template #footer>
      <span class="dialog-footer">
        <el-button @click="popupVisible = false">Cancel</el-button>
        <el-button type="primary" @click="sendMessage">
          Confirm
        </el-button>
      </span>
        </template>
    </el-dialog>

</template>

<script>
import {ElMessageBox, ElNotification} from "element-plus";

export default {
    name: 'Fields',
    emits: ['send'],
    props: {
        field: {
            type: Object,
            required: true
        },
        fieldIndex: {
            type: Number,
            default: 0
        },
        isSenderPrefix: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            error: false,
            addresses: [],
            timer: false,

            popupVisible: false,

            integrations: [
                {
                    name: 'sms_center',
                    title: 'smsCenter',
                    value: false,
                },
                {
                    name: 'telegram',
                    title: 'Телеграм',
                    value: false,
                }
            ],
            message: '',
        }
    },
    methods: {
        findAddress(query) {
            query = !!query ? query : this.field.pivot.value;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                axios.get('/admin/field/find-address?search=' + query)
                    .then((response) => {
                        this.addresses = response.data.data.suggestions
                    })
            }, 1000)
        },
        check(e) {
            let field = this.field;

            this.checkPhone(field);


            this.error = false;
            if (field.is_required) {
                if (!field.pivot.value) {
                    this.error = true;
                }
            }
        },
        checkPhone(field) {
            if (field.type.name === 'phone') {
                this.field.pivot.value = field.pivot.value.replace(/[^0-9^+-]/g,'');
            }
        },
        sendMessage() {
            axios.post('/admin/sender/send', {
                'integrations': this.integrations,
                'message': this.message,
                'recipient': this.field.pivot.value
            }).then((response) => {
                this.popupVisible = false;
                this.message = '';

                if (!!response.data.errors) {
                    response.data.errors.forEach((error) => {
                        if (!!error) {
                            setTimeout(() => {
                                ElNotification({
                                    duration: 8000,
                                    title: 'Ошибка',
                                    message: error,
                                    type: 'error',
                                })
                            }, 100);
                        }
                    })
                }

                if (!!response.data.messages) {
                    response.data.messages.forEach((message) => {
                        setTimeout(() => {
                            ElNotification({
                                title: 'Доставлено',
                                message: message,
                                type: 'success',
                            })
                        }, 100);
                    })
                }
            });
        },
        call() {
            ElMessageBox.confirm(
                'Продолжить?',
                'Позвонить',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'success',
                }
            )
            .then(() => {
                    axios.post('/admin/telephony/call', {
                        phone: this.field.pivot.value
                    });
                }
            );
        },
        send() {
            if (this.error) {
                ElNotification({
                    title: 'Поле обязательно',
                    type: 'error',
                    position: 'bottom-right',
                });
            } else {
                this.$emit('update:modelValue', this.$el.innerHTML);
                this.$emit('send');
            }
        }
    }
}
</script>

<style scoped>
.phone-action > i {
    cursor: pointer;
}
.phone-action > i:hover {
    filter: brightness(0.4);
}
</style>
