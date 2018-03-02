<template>
    <div class="modal-card" style="width: auto">
        <header class="modal-card-head">
            <p class="modal-card-title">{{ title }}</p>
        </header>

        <section class="modal-card-body">
            <b-field label="种类">
                <b-select placeholder="请选择种类" v-model="categories" autofocus required rounded expanded>
                    <option v-for="o in allCategories" :value="o.name" :key="o.id"> {{ o.name }} </option>
                </b-select>
            </b-field>

            <b-field label="收益">
                <b-input v-model="profits" type="number" placeholder="请输入收益" @keyup.native.enter="dosubmit" rounded required> </b-input>
            </b-field>

            <b-field label="日期">
                <b-datepicker v-model="optime" :date-formatter="dfmat" :date-parser="dparser" placeholder="点击选择日期"></b-datepicker>
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
    props: ['title', 'allCategories', 'id', 'categories', 'profits', 'optime'],

    methods: {
        dosubmit(){
            let optimeString = this.dfmat(this.optime);
            const params = {'categories': this.categories, 'profits': this.profits, 'optime': optimeString};
            if(this.categories === undefined || this.categories === '' || this.profits === 0){
                this.$dialog.alert('种类或收益不合法');
            }else{
                if(this.id === undefined){
                    this.sendReq('/index/addData', params);
                }else{
                    this.sendReq('/index/updateData', {id: this.id, ...params});
                }
            }
        },

        sendReq(uri, params){
            axios.post(uri, qs.stringify(params))
                .then((response) => {
                    if(response.data.status === 'OK'){
                        this.success();
                        this.$emit('close');
                        this.$emit('updateData');
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
        },

        dfmat(d){
            const y = d.getFullYear();

            let m = d.getMonth() + 1;
            m = m < 10 ? '0' + m : m;

            let day = d.getDate();
            day = day < 10 ? '0'+day: day;

            return y + '-' + m + '-' + day;
        },

        dparser(d){
            const t = d.split('-');
            const nd = t[1] + '-' + t[2] + '-' + t[1];
            return new Date(Date.parse(nd))
        }
    }
};
</script>
