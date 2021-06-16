import { HorizontalBar } from 'vue-chartjs'

export default {
  
  extends: HorizontalBar,
  
  props: {
    
    data : { type : Array, default : ()=>[]},

    colors : { type : Array, default : ()=> []}
  },
  
  mounted () {

    var newset = [];

    var set = this.data.data.assets;

    for(var i in set){

      newset.push({ 
        label: set[i].name,
        backgroundColor: this.colors[i],
        hoverBackgroundColor: this.colors[i],
        hoverBorderWidth: 2,
        data: [set[i].count]
      })
    }

    this.renderChart({

      datasets: newset
    }, {
      
      responsive: true, 
      
      maintainAspectRatio: false,

      tooltips: {
      
        callbacks: {
      
           title: function(data1,data2) {

            return ''
           }
        }
      },
      
      scales: {
        
         yAxes: [{
      
          scaleLabel: {
      
            display: true,
      
            labelString: 'Asset types'
          },
        }],

        xAxes: [{
          
          scaleLabel: {
            
            display: true,
            
            labelString: 'Count'
          },
          
          ticks: {
      
            stepSize: 1,
            
            min: 0
          }
        }],
      }
    })
  }
}