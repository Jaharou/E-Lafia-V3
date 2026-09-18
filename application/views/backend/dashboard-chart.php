<script>
            $(function($) {
                // chart1
                var chart1 = AmCharts.makeChart( "chart1", {
                  "type": "pie",
                  "theme": "light",
                  "fontFamily": "Poppins",
                  "dataProvider": [ {
                    "country": "<?php echo get_phrase('consultation') ?>",
                    "litres": <?php echo $this->db->count_all('invoice'); ?>
                  }, {
                    "country": "<?php echo get_phrase('examen') ?>",
                    "litres": <?php echo $this->db->count_all('examen'); ?>
                  }, /*{
                    "country": "<?php echo get_phrase('infirmière') ?>",
                    "litres": <?php echo $this->db->count_all('nurse'); ?>
                  },{
                    "country": "<?php echo get_phrase('docteur') ?>",
                    "litres": <?php echo $this->db->count_all('doctor'); ?>
                  }*/],
                  "valueField": "litres",
                  "titleField": "country",
                   "balloon":{
                   "fixedPosition":true
                  },
                  "export": {
                    "enabled": true
                  }
                } );
          } );
</script>