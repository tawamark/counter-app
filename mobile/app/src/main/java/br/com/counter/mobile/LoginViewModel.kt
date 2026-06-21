package br.com.counter.mobile

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.launch

class LoginViewModel(application: Application) : AndroidViewModel(application) {
    private val repository = AuthRepository(application.applicationContext)
    private val loadingValue = MutableLiveData(false)
    private val errorValue = MutableLiveData<String?>()
    private val authenticatedValue = MutableLiveData(repository.isLoggedIn())

    val loading: LiveData<Boolean> = loadingValue
    val error: LiveData<String?> = errorValue
    val authenticated: LiveData<Boolean> = authenticatedValue

    fun login(email: String, password: String) {
        if (email.isBlank() || password.isBlank()) {
            errorValue.value = "Informe e-mail e senha."
            return
        }

        loadingValue.value = true
        errorValue.value = null

        viewModelScope.launch {
            val result = repository.login(email, password)
            loadingValue.value = false

            if (result.isSuccess) {
                authenticatedValue.value = true
            } else {
                errorValue.value = result.exceptionOrNull()?.message ?: "Não foi possível realizar o login."
            }
        }
    }
}
