package br.com.counter.mobile

import android.content.Context
import android.view.Gravity
import android.widget.Toast

object UiFeedback {
    fun showToast(context: Context, message: String) {
        Toast.makeText(context, message, Toast.LENGTH_SHORT).apply {
            setGravity(Gravity.CENTER, 0, 0)
            show()
        }
    }
}
