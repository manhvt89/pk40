$(document).ready(function() {
  // Danh sách thuốc đã lưu trữ dưới dạng JSON
  var medicationList = [
    {
      "name": "Paracetamol",
      "dosage": "500mg",
      "instructions": "Uống 1 viên mỗi 6 giờ"
    },
    {
      "name": "Amoxicillin",
      "dosage": "250mg",
      "instructions": "Uống 1 viên mỗi 8 giờ"
    },
    {
      "name": "Ibuprofen",
      "dosage": "200mg",
      "instructions": "Uống 1 viên mỗi 4 giờ"
    }
  ];

  // Tìm kiếm thuốc
  $('#search-input').on('input', function() {
    var searchQuery = $(this).val().toLowerCase();
    var searchResults = [];

    if (searchQuery.length > 0) {
      searchResults = medicationList.filter(function(medication) {
        return medication.name.toLowerCase().includes(searchQuery);
      });
    }

    displaySearchResults(searchResults);
  });

  // Hiển thị kết quả tìm kiếm
  function displaySearchResults(results) {
    var searchResultsContainer = $('#search-results');
    searchResultsContainer.empty();

    results.forEach(function(medication) {
      var listItem = $('<li></li>').text(medication.name);
      listItem.on('click', function() {
        addMedicationToPrescription(medication);
      });

      searchResultsContainer.append(listItem);
    });
  }
  
  // Thêm thuốc vào đơn thuốc
  function addMedicationToPrescription(medication) {
    var prescriptionList = $('#prescription-list');
    var listItem = $('<li></li>');

    // Tạo nội dung cho mỗi thuốc trong đơn thuốc
    var medicationContent = $('<div class="medication-content"></div>');
    medicationContent.append($('<h3></h3>').text(medication.name));
    medicationContent.append($('<p></p>').text('Liều lượng: ' + medication.dosage));
    medicationContent.append($('<p></p>').text('Hướng dẫn: ' + medication.instructions));

    // Tạo nút xóa thuốc khỏi đơn thuốc
    var removeButton = $('<button class="remove-button">Xóa</button>');
    removeButton.on('click', function() {
      listItem.remove();
    });

    listItem.append(medicationContent);
    listItem.append(removeButton);
    prescriptionList.append(listItem);
  }
});

