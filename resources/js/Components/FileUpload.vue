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
                <div v-if="isImage(file.type)">
                    <img class="img-thumbnail" :src="file.full_path">
                    <div class="name">{{file.name}}</div>
                    <div class="size">{{ definitionSize(file.size) }} Кб.</div>
                    <i class="las la-trash-alt" @click="removeFile(file)" />
                </div>
                <div v-else>
                    <i class="las la-file"/>
                    <div class="name">{{file.name}}</div>
                    <div class="size">{{ definitionSize(file.size) }} Кб.</div>
                    <i class="las la-trash-alt" @click="removeFile(file)" />
                </div>
                <hr>
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
    emits: ['send'],
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
                        file.full_path = e.target.result;
                        this.files.push(file);
                    }

                    reader.readAsDataURL(file);
                })
            }
        },
        removeFile(file) {
            this.files.forEach((item, index) => {
                if (item.full_path === file.full_path) {
                    this.files.splice(index, 1);
                }
            });
        },
        send() {
            this.$emit('send', this.files);
            this.files = [];
        },
        openDirectory() {
            const elem = this.$refs.inputFile
            elem.click()
        },
        definitionSize(item) {
            return Math.floor(item / 1024)
        },
        isImage(meme) {
            switch (meme) {
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/png':
                case 'image/svg':
                    return true
                default:
                    return false
            }
        },

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
.file-upload__info-area {
    width: 100%;
}
.file-upload .file-upload__info-area {
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}
.file-upload__item {
    width: 100%;
}
.file-upload .file-upload__item {
    min-height: max-content;
}
.file-upload .file-upload__item > div {
    display: inline-flex;
    gap: 10px;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.file-upload .img-thumbnail {
    max-width: 64px;
}
.file-upload .name {
    overflow: hidden;
}
.file-upload .size {
    white-space: nowrap;
}
.la-trash-alt {
    cursor: pointer;
    font-size: 20px;
    color: red;
}
i.la-trash-alt:hover {
    filter: brightness(80%);
}
</style>
