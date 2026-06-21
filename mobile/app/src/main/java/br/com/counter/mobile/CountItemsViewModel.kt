package br.com.counter.mobile

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.launch

class CountItemsViewModel(application: Application) : AndroidViewModel(application) {
    private val repository = InventoryRepository(application.applicationContext)
    private val itemsValue = MutableLiveData<List<CountItemEntity>>()
    private val messageValue = MutableLiveData<String?>()
    private val loadingValue = MutableLiveData(false)
    private val savingItemIdValue = MutableLiveData<Int?>()
    private val syncedValue = MutableLiveData(false)

    val items: LiveData<List<CountItemEntity>> = itemsValue
    val message: LiveData<String?> = messageValue
    val loading: LiveData<Boolean> = loadingValue
    val savingItemId: LiveData<Int?> = savingItemIdValue
    val synced: LiveData<Boolean> = syncedValue

    fun load(countId: Int) {
        viewModelScope.launch {
            itemsValue.value = repository.localItems(countId)
            val result = repository.refreshItems(countId)

            if (result.isSuccess) {
                itemsValue.value = result.getOrNull()
            } else {
                messageValue.value = result.exceptionOrNull()?.message
            }
        }
    }

    fun saveQuantity(countId: Int, itemId: Int, value: String) {
        viewModelScope.launch {
            messageValue.value = null

            val normalizedValue = value.trim().replace(",", ".")
            val quantity = if (normalizedValue.isBlank()) null else normalizedValue.toDoubleOrNull()

            if (normalizedValue.isNotBlank() && quantity == null) {
                messageValue.value = "Informe uma quantidade válida."
                return@launch
            }

            if (quantity != null && quantity < 0) {
                messageValue.value = "A quantidade não pode ser negativa."
                return@launch
            }

            savingItemIdValue.value = itemId
            repository.saveQuantity(itemId, quantity)
            itemsValue.value = repository.localItems(countId)
            savingItemIdValue.value = null
            messageValue.value = if (quantity == null) {
                "Quantidade removida localmente."
            } else {
                "Quantidade salva localmente."
            }
        }
    }

    fun sync(countId: Int) {
        viewModelScope.launch {
            loadingValue.value = true
            messageValue.value = null
            val result = repository.syncItems(countId)
            loadingValue.value = false

            if (result.isSuccess) {
                syncedValue.value = true
                itemsValue.value = repository.localItems(countId)
                messageValue.value = "Itens sincronizados com sucesso."
            } else {
                messageValue.value = result.exceptionOrNull()?.message
            }
        }
    }
}
