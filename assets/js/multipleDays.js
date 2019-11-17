import React from 'react';
import ReactDOM from 'react-dom';
import DayPicker, { DateUtils } from 'react-day-picker';
import 'react-day-picker/lib/style.css';

export default class Picker extends React.Component {
  constructor(props) {
    super(props);
    this.handleDayClick = this.handleDayClick.bind(this);

    // Récupérer les dates, enlever les crochets, en faire un tableau
    var dates = document.getElementById("vacation_days").value;
    dates = dates.substring(1, dates.length - 1);
    dates = dates.split(',');
    var selectedDates = [];
    dates.forEach(element => {
      selectedDates.push(new Date(element.substr(7, 4), element.substr(4, 2) - 1, element.substr(1, 2)));
    });

    this.state = {
      selectedDays: selectedDates,
    };
  }
  
  handleDayClick(day, { selected }) {
    const { selectedDays } = this.state;
    if (selected) {
      const selectedIndex = selectedDays.findIndex(selectedDay =>
        DateUtils.isSameDay(selectedDay, day)
      );
      selectedDays.splice(selectedIndex, 1);
    } else {
      selectedDays.push(day);
    }
    this.setState({ selectedDays });

    // On met chaque dates dans un tableau
    var dates = [];
    this.state.selectedDays.forEach(element => {
      dates.push(element.toLocaleDateString("fr-FR"));
    });
    // On met ce tableau dans le input correspondant
    document.getElementById("vacation_days").value = JSON.stringify(dates).slice(1,-1);
  }

  render() {
    return (
      <div>
        <DayPicker
          selectedDays={this.state.selectedDays}
          onDayClick={this.handleDayClick}
        />
      </div>
    );
  }
}

ReactDOM.render(<Picker />, document.getElementById('form-datepicker-multiple'));