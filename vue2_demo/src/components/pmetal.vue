<template>
    <section>
        <center><strong>{{ dyzby }} </strong></center>
        <b-table :data="icbcdata" :loading="isLoading" striped hoverable mobile-cards>
            <template slot-scope="props">
                <b-table-column field="category" label="品种">
                    {{ props.row.category}}
                </b-table-column>

                <b-table-column field="direct" label="涨跌方向">
                    <img :src="props.row.direct">
                </b-table-column>

                <b-table-column field="bank_buy_price" label="银行买入价">
                    {{ props.row.bank_buy_price}}
                </b-table-column>

                <b-table-column field="bank_sell_price" label="银行卖出价">
                    {{ props.row.bank_sell_price}}
                </b-table-column>

                <b-table-column field="middle_price" label="中间价">
                    {{ props.row.middle_price}}
                </b-table-column>

                <b-table-column field="hightest_price" label="最高中间价">
                    {{ props.row.hightest_price}}
                </b-table-column>

                <b-table-column field="lowest_preice" label="最低中间价">
                    {{ props.row.lowest_preice}}
                </b-table-column>

                <b-table-column field="trend" label="走势图">
                    <a :href="props.row.trend" target="_blank"><img src="img/zoushi.gif"></a>
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
    </section>
</template>

<script>
    import zoushi from '../img/zoushi.gif';
    import rise from '../img/0.gif';
    import down from '../img/1.gif';
    import axios from 'axios';
    export default {
        data () {
            return {
                dyzby: '',
                icbcdata:[],
                isLoading: false,
                timer: null
            }
        },

        created () {
            this.getData();
            this.reloadSelf();
        },

        methods: {
            getData(){
                this.isLoading = true;
                axios.get('/pmetal/getInfo')
                    .then((response) => {
                        this.dyzby = response.data.dyzby;
                        this.icbcdata = response.data.icbc;
                        this.isLoading = false;
                    })
                    .catch((err) => {
                        console.log(err);
                        this.isLoading = false;
                    });
            },

            reloadSelf () {
                this.timer= setInterval(() => {
                    this.getData();
                }, 3000);
            }
        },

        beforeDestroy () {
            clearInterval(this.timer);
        }
    };
</script>
