<template>
    <div class="file-upload">
        <div class="file-upload__area" @click="openDirectory">
            Выберите файлы
            <input
                type="file"
                name=""
                id=""
                multiple
                ref="inputFile"
                @change="handleFileChange($event)"
            />
        </div>
        <div class="file-upload__info-area">
            <div class="file-upload__item" v-for="(file, index) in files">
                <div v-if="file.type === `image/png`">
                    <div class="file-remove" @click="removeFile(file)">x</div>
                    <img class="img-thumbnail" :src="file.base64">
                </div>
                <div v-else>
                    <div class="file-remove" @click="removeFile(file)">x</div>
                    <div class="name">{{file.name}}</div>
                    <div class="size">{{ definitionSize(file.size) }} Кб.</div>
                </div>
            </div>

            <div class="file-upload__item" v-for="(doc, index) in docsData">
                <div class="name">{{doc.name}}</div>
                <div class="size">{{ definitionSize(doc) }} Кб.</div>
            </div>
        </div>
        <el-button
            class="w-100 mt-3"
            type="success"
            @click="send"
        >
            Отправить
        </el-button>
    </div>
</template>

<script>
export default {
    name: 'FileUpload',
    data() {
        return {
            imagesData: [],
            docsData: [],
            files: []
        }
    },
    methods: {
        handleFileChange(e) {
            let target = e.target;
            if (target && target.files) {
                var files = target.files;
                files.forEach((file) => {
                    var reader = new FileReader();
                    reader.onload = (e) => {
                        file.base64 = e.target.result;
                        this.files.push(file);
                    }

                    reader.readAsDataURL(file);
                })
            }
        },
        removeFile(file) {
            this.files.forEach((item, index) => {
                if (item.base64 === file.base64) {
                    this.files.splice(index, 1);
                }
            });
        },
        send() {
            this.$emit('send', this.files);
        },
        openDirectory() {
            const elem = this.$refs.inputFile
            elem.click()
        },
        definitionSize(item) {
            return Math.floor(item / 1024)
        }

    }
}
</script>

<style scoped>
input[type="file"] {
    display: none;
}
.file-upload {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
}
.file-upload .file-upload__area {
    width: 100%;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ccc;
    margin: 0 0 40px 0;
}
.file-upload .file-upload__info-area {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}
.file-upload .file-upload__item {
    min-height: max-content;
    width: 100px;
}
.file-upload .img-thumbnail {
    min-height: 64px;
    min-width: 100%;
}

.file-remove {
    cursor: pointer;
}
</style>
