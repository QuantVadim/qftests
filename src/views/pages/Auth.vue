<template>
    <component :is="socnet"></component>
</template>

<script>
import vk from '../auth/vk';

export default {
    components:{
        vk
    },
    computed:{
        socnet(){
            return this.$route.params.socnet;
        }
    }
}
</script>