import {Pie} from 'vue-chartjs'

export default {
  
  extends: Pie,
  
  props: {
    
    data : { type : Array, default : ()=>[]},

    colors : { type : Array, default : ()=> []}
  },
  
  mounted () {

    this.renderChart({

      labels: this.data.data.assets.map(item => item.name),

      datasets: [
        {
          backgroundColor: this.colors,
          data: this.data.data.assets.map(item => item.count)
        }
      ]
    }, {
      responsive: true, 
      maintainAspectRatio: false,
    })
  }
}