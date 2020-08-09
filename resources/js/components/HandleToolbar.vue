<script> 
export default {    
    computed: {
        showToolbar: function() { 
            return this.field.showToolbar && _.keys(this.field.locales).length > 1;
        }
    },
}
</script>
