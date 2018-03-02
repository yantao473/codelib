<template>
    <div class="modal-card" style="width: auto">
        <header class="modal-card-head">
            <p class="modal-card-title">{{ title }}</p>
        </header>

        <section class="modal-card-body">
            <b-field label="名称">
                <b-input v-model="name" @keyup.native.enter="dosubmit" maxlength=20 placeholder="请输入名称" autofocus rounded required></b-input>
            </b-field>
            <b-field label="备注">
                <b-input v-model="notes" @keyup.native.enter="dosubmit" maxlength=40 placeholder="请输入备注" rounded required> </b-input>
            </b-field>
        </section>

        <footer class="modal-card-foot">
            <button class="button" type="button" @click="$parent.close()">取消</button>
            <button class="button is-primary" @click="dosubmit">提交</button>
        </footer>
    </div>
</template>

<script>
import qs from 'qs';
import axios from 'axios';
export default {
    props: ['title', 'id', 'name', 'notes'],

    methods: {
        dosubmit(){
            const params = {'name': this.name, 'notes': this.notes};
            if(this.name ==='' || this.notes === ''){
                this.$dialog.alert('名称或备注不能为空');
            }else{
                if(this.id === undefined){
                    this.sendReq('/index/addCategory', params);
                }else{
                    this.sendReq('/index/updateCategory', {'id': this.id, ...params});
                }
            }
        },

        sendReq(uri, params){
            axios.post(uri, qs.stringify(params))
                .then((response) => {
                    if(response.data.status === 'OK'){
                        this.success();
                        this.$emit('close');
                        this.$emit('updateCategory');
                    }else{
                        this.failed();
                    }
                })
                .catch((err) => {
                    this.$dialog.alert('未知错误: ' + err)
                });
        },

        success(){
            this.$toast.open({
                message: '种类操作成功',
                type: 'is-success'
            });
        },

        failed(){
            this.$toast.open({
                message: '种类操作失败',
                type: 'is-danger'
            });
        }
    }
};
</script>
