<template>
    <section>
        <b-tabs v-model="activeTab" @change="tabChange">
            <b-tab-item label="数据">
                <button class="button is-info is-rounded btnmargin" @click="addData()"><i class="fa fa-plus" aria-hidden="true"></i> 添加数据</button>
                <b-table :data="isEmpty ? [] : data" :loading="isLoading" striped hoverable mobile-cards>
                    <template slot-scope="props">
                        <b-table-column field="categories" label="分类">
                            {{ props.row.categories }}
                        </b-table-column>

                        <b-table-column field="profits" label="收益">
                            <span class="pfont" :class="bpfont(props.row.profits)">
                                {{ Number.parseFloat(props.row.profits).toFixed(2) }}
                            </span>
                        </b-table-column>

                        <b-table-column field="optime" label="日期" centered>
                            {{ props.row.optime }}
                        </b-table-column>

                        <b-table-column  label="操作" centered>
                            <b-tooltip label="编辑">
                                <button class="button" @click="editData(props.row)"><i class="fa fa-edit" aria-hidden="true"></i></button>
                            </b-tooltip>
                            <b-tooltip label="删除">
                                <button class="button" @click="deleteData(props.row)"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </b-tooltip>
                        </b-table-column>
                    </template>

                    <template slot="footer" v-if="!isEmpty">
                        <div class="has-text-right">
                            总计：<span class="pfont" :class="bpfont(getTotal)">{{ getTotal }}</span>
                        </div>
                    </template>

                    <template slot="empty">
                        <section class="section">
                            <div class="content has-text-grey has-text-centered">
                                <p>没有数据</p>
                            </div>
                        </section>
                    </template>
                </b-table>
            </b-tab-item>

            <b-tab-item label="种类">
                <button class="button is-info is-rounded btnmargin" @click="addCategory"><i class="fa fa-plus" aria-hidden="true"></i> 添加种类</button>
                <b-table :data="categoryData" :loading="isLoading" striped hoverable mobile-cards>
                    <template slot-scope="props">
                        <b-table-column field="name" label="名称">
                            {{ props.row.name}}
                        </b-table-column>

                        <b-table-column field="notes" label="备注">
                            {{ props.row.notes}}
                        </b-table-column>

                        <b-table-column  label="操作" centered>
                            <b-tooltip label="编辑">
                                <button class="button" @click="editCategory(props.row)"><i class="fa fa-edit" aria-hidden="true"></i></button>
                            </b-tooltip>
                            <b-tooltip label="删除">
                                <button class="button" @click="deleteCategory(props.row)"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </b-tooltip>
                        </b-table-column>
                    </template>

                    <template slot="empty">
                        <section class="section">
                            <div class="content has-text-grey has-text-centered">
                                <p>没有数据</p>
                            </div>
                        </section>
                    </template>
                </b-table>
            </b-tab-item>
        </b-tabs>
    </section>
</template>

<style>
.pfont {
    font-weight: bold;
}

.surplus {
    color: red;
}

.deficit {
    color: green;
}
.btnmargin {
    margin-bottom: 8px;
}
</style>

<script>
import qs from 'qs';
import axios from 'axios';
import modalData from './modal_index_data.vue';
import modalCategory from './modal_index_category.vue';

export default {
    data() {
        return {
            activeTab: 0,
            data: [],
            categoryData: [],
            isEmpty: true,
            isLoading: false
        };
    },

    methods: {
        getBaseData(){
            return axios.get('/index/getData');
        },

        getCategoryData(){
            return axios.get('/index/getCategories');
        },

        updateCategory(){
            this.getCategoryData().then((response) => {
                this.categoryData = response.data;
            }).catch((err) => {
                this.$dialog.alert('各类更新失败: ' + err);
            });
        },

        updateData () {
            this.getBaseData().then((response) => {
                this.data = response.data;
            }).catch((err) => {
                this.$dialog.alert('各类更新失败: ' + err);
            });
        },

        loadAsyncData(){
            this.loading = true;
            axios.all([this.getBaseData(), this.getCategoryData()])
                .then(axios.spread((data, categories) => {
                    this.isEmpty = false;
                    this.loading = false;
                    this.data = data.data;
                    this.categoryData = categories.data;
                }))
                .catch(() => {
                    this.isEmpty = true;
                    this.loading = false;
                });
        },

        bpfont (value) {
            const v = Number.parseFloat(value);
            if (v <= 0){
                return 'deficit';
            }else{
                return 'surplus';
            }
        },

        addData(){
            const curDate = new Date();
            const selData = {title: '添加数据', allCategories: this.categoryData, id: undefined, categories: undefined, profits: 0, optime: curDate};
            const events = {'updateData': this.updateData};
            this.openModal(selData, modalData, events);
        },

        editData(rowdata){
            console.log(this.data);
            let selData = {title: '编辑数据', allCategories: this.categoryData,  ...rowdata};
            selData.optime = new Date(selData.optime);
            const events = {'updateData': this.updateData};
            this.openModal(selData, modalData, events);
        },

        deleteData(rowdata){
            this.$dialog.confirm({
                title: '删除数据',
                message: '确认删除这条数据: <strong>' + rowdata.categories + '  ' + rowdata.profits + '  ' + rowdata.optime +'</strong>?',
                cancelText: '取消',
                confirmText: '确定',
                hasIcon: true,
                onConfirm: () => {
                    axios.post('/index/deleteData', qs.stringify({id: rowdata.id}))
                        .then((response) => {
                            if(response.data.status === 'OK'){
                                this.success('数据删除成功');
                                let index = this.data.findIndex((v, i, a) => v.id === rowdata.id);
                                this.data.splice(index, 1);
                            }else{
                                this.success('数据删除失败');
                            }
                            this.activeTab = 0;
                        })
                        .catch((err) => {
                            this.$dialog.alert('未知错误: ' + err);
                        });
                }
            });
        },

        addCategory(){
            const selData = {title: '添加种类', id: undefined, name: '', 'notes': ''};
            const events = {'updateCategory': this.updateCategory};
            this.openModal(selData, modalCategory, events);
        },

        editCategory(rowdata){
            const selData = {title: '编辑种类', ...rowdata};
            const events = {'updateCategory': this.updateCategory};
            this.openModal(selData, modalCategory, events);
        },

        deleteCategory(rowdata){
            this.$dialog.confirm({
                title: '删除种类',
                message: '确认删除种类' +rowdata.name + '?',
                cancelText: '取消',
                confirmText: '确定',
                hasIcon: true,
                onConfirm: () => {
                    axios.post('/index/deleteCategory', qs.stringify({id: rowdata.id}))
                        .then((response) => {
                            if(response.data.status === 'OK'){
                                this.success('种类删除成功');
                                let index = this.categoryData.findIndex((v, i, a) => v.id === rowdata.id);
                                this.categoryData.splice(index, 1);
                            }else{
                                this.success('种类删除失败');
                            }
                            this.activeTab = 1;
                        })
                        .catch((err) => {
                            this.$dialog.alert('未知错误: ' + err);
                        });
                }
            });
        },

        openModal(props, component, events){
            this.$modal.open({
                props,
                component,
                events,
                parent: this,
                canCancel: false,
                hasMobileCard: true
            });
        },

        success(message){
            this.$toast.open({
                message,
                type: 'is-success'
            });
        },

        failed(message){
            this.$toast.open({
                message,
                type: 'is-danger'
            });
        }
    },

    mounted () {
        this.loadAsyncData();
    },

    computed: {
        getTotal(){
            let total = 0;
            for(let v of this.data){
                total += Number.parseFloat(v.profits);
            }
            return total.toFixed(2);
        }
    }
};
</script>
