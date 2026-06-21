package br.com.counter.mobile

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.launch

class MainViewModel(application: Application) : AndroidViewModel(application) {
    private val authRepository = AuthRepository(application.applicationContext)
    private val inventoryRepository = InventoryRepository(application.applicationContext)
    private val countsValue = MutableLiveData<List<InventoryCountEntity>>()
    private val summaryValue = MutableLiveData<SummaryData?>()
    private val messageValue = MutableLiveData<String?>()
    private val loadingValue = MutableLiveData(false)
    private val loggedOutValue = MutableLiveData(false)

    val counts: LiveData<List<InventoryCountEntity>> = countsValue
    val summary: LiveData<SummaryData?> = summaryValue
    val message: LiveData<String?> = messageValue
    val loading: LiveData<Boolean> = loadingValue
    val loggedOut: LiveData<Boolean> = loggedOutValue

    fun userLabel(): String {
        val name = authRepository.userName()
        val email = authRepository.userEmail()

        return if (name.isBlank()) email else "$name • $email"
    }

    fun load() {
        viewModelScope.launch {
            countsValue.value = inventoryRepository.localCounts()
            loadSummary()
            refreshCounts()
        }
    }

    fun refresh() {
        viewModelScope.launch {
            loadingValue.value = true
            messageValue.value = null
            loadSummary()
            val refreshed = refreshCounts()
            loadingValue.value = false

            if (refreshed) {
                messageValue.value = "Contagens atualizadas com sucesso."
            }
        }
    }

    fun logout() {
        viewModelScope.launch {
            authRepository.logout()
            loggedOutValue.value = true
        }
    }

    private suspend fun loadSummary() {
        val result = inventoryRepository.summary()
        if (result.isSuccess) {
            summaryValue.value = result.getOrNull()
        } else {
            messageValue.value = result.exceptionOrNull()?.message
        }
    }

    private suspend fun refreshCounts(): Boolean {
        val result = inventoryRepository.refreshCounts()
        return if (result.isSuccess) {
            countsValue.value = result.getOrNull()
            true
        } else {
            messageValue.value = result.exceptionOrNull()?.message
            false
        }
    }
}
