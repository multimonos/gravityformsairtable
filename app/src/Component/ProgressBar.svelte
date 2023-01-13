<script>
    export let value
    export let max
</script>

<progress value="{value}" max="{max}">{value}</progress>
<p>&nbsp;&nbsp; {value} of {max} Processed</p>
