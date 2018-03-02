<template>
    <section class="hero is-success">
        <div class="container is-fluid has-text-centered">
            <div class="column is-4 is-offset-4">
                <div class="box content">
                    <h3 class="title">登入系统</h3>
                    <b-field>
                        <b-input v-model.trim="username" @keyup.native.enter="dologin" icon="user" placeholder="请输入用户名" rounded></b-input>
                    </b-field>

                    <b-field>
                        <b-input type="password" v-model.trim="password" @keyup.native.enter="dologin" icon="lock" placeholder="请输入密码" password-reveal rounded></b-input>
                    </b-field>

                    <b-field v-if="0==1" class="is-horizontal">
                        <b-checkbox  true-value="yes" false-vlue="no" v-model="reme">记住密码</b-checkbox>
                    </b-field>

                    <b-field>
                        <button @click="dologin" class="button is-block is-large is-fullwidth is-primary is-round">登录</button>
                    </b-field>
                </div>
            </div>
        </div>
    </section>
</template>

<style>
.hero.is-success {
    background: #F2F6FA;
    height:100%;
    width: 100%;
}

.button.is-round {
    border: 1px solid;
    border-radius: 25px;
}

.box.content {
    margin-top: 7rem;
    border-style:solid;
    border-width:thin;
    border-color: #ddd;
    box-shadow: 10px 10px 5px #888;
    border-radius:15px;
    background-color: white;
}
</style>

<script>
import qs from 'qs';
import axios from 'axios';

export default {
    data () {
        return {
            username: '',
            password: '',
            reme: false
        }
    },

    methods: {
        dologin(){
            let params = {
                username: this.username,
                password: this.password,
                reme: this.reme
            };

            axios.post('/user/dologin', qs.stringify(params))
                .then((response) => {
                    let data = response.data;
                    if(data.status == 'OK'){
                        this.$store.dispatch('dologin', this.username);
                        let redirect = decodeURIComponent(this.$route.query.redirect || '/');
                        this.$router.push({path: redirect});
                        this.$cookie.set('loginUser', this.username, {expires: '1D'});
                    }else{
                        this.$dialog.alert('用户名或密码错误,请重新登录')
                        this.username = '';
                        this.password = '';
                        this.$router.push({path:'/login'});
                    }
                })
                .catch((error)=>{
                    this.$dialog.alert('未知错误: ' + error)
                });
        }
    }
};
</script>
