export default{
    getDataHash(name) {
        const pattern = /([.$?*|{}()[]\\\/\+^])/g;
        let matches = window.location.href.split('#')[1].match(new RegExp(
        "(?:^|&)" + name.replace(pattern, '\\$1') + "=([^&]*)"));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    },
    getCookie(name) {
        let matches = document.cookie.match(new RegExp(
          "(?:^|; )" + name.replace(/([.$?*|{}()[]\/+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
    
}