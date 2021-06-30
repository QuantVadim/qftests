<template>
<div class="center-loading">
    <it-loading></it-loading><br>
    <div>Выполняется вход</div>
</div>
</template>

<script scope>
import helpers from '../../others/helpers'
import {mapActions} from 'vuex'


export default{
    data(){
        return{
            link: '',
        }
    },
mounted(){
    this.axios.post('http://t.myqf.ru/backend/auth.php', {
	user_id: helpers.getDataHash('user_id'),
	access_token: helpers.getDataHash('access_token'),
	sn: 'vk'
}).then(respon => {
	console.log(respon);
    
	if(!respon.data?.error){
		document.cookie = "usrid="+respon.data.usr_id+"; max-age=3600000; path=/; SameSite=None; Secure";
		document.cookie = "mykey="+respon.data.mykey+"; max-age=3600000; path=/; SameSite=None; Secure";
        this.LOAD_USER();
        this.$router.push('/');
	}
}).catch(itm=>{alert(itm)});
    },
    methods:{
        ...mapActions(["LOAD_USER"]),
    }

}


</script>

<style>
.center-loading{
    text-align: center;
    padding-top: 60px;
}

</style>