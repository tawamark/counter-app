package br.com.counter.mobile

import android.content.Context
import androidx.room.Database
import androidx.room.Room
import androidx.room.RoomDatabase

@Database(entities = [InventoryCountEntity::class, CountItemEntity::class], version = 1, exportSchema = false)
abstract class CounterDatabase : RoomDatabase() {
    abstract fun counterDao(): CounterDao

    companion object {
        private lateinit var instance: CounterDatabase

        fun getDatabase(context: Context): CounterDatabase {
            if (!::instance.isInitialized) {
                synchronized(CounterDatabase::class.java) {
                    instance = Room.databaseBuilder(context, CounterDatabase::class.java, "counter_mobile")
                        .build()
                }
            }

            return instance
        }
    }
}
