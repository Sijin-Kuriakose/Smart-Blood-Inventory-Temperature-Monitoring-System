-- =============================================
-- Blood Inventory System - SQL Server Database Dump
-- Database: blood_inventory
-- Generated: 2026-05-26
-- Server: Microsoft SQL Server (sqlsrv)
-- =============================================

USE [master];
GO

-- Create database if it doesn't exist
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = N'blood_inventory')
BEGIN
    CREATE DATABASE [blood_inventory];
END
GO

USE [blood_inventory];
GO

-- =============================================
-- DROP EXISTING TABLES (in FK dependency order)
-- =============================================

IF OBJECT_ID('dbo.temperature_logs', 'U') IS NOT NULL DROP TABLE [dbo].[temperature_logs];
IF OBJECT_ID('dbo.alerts', 'U') IS NOT NULL DROP TABLE [dbo].[alerts];
IF OBJECT_ID('dbo.blood_bags', 'U') IS NOT NULL DROP TABLE [dbo].[blood_bags];
IF OBJECT_ID('dbo.blood_bank_user', 'U') IS NOT NULL DROP TABLE [dbo].[blood_bank_user];
IF OBJECT_ID('dbo.refrigerators', 'U') IS NOT NULL DROP TABLE [dbo].[refrigerators];
IF OBJECT_ID('dbo.blood_banks', 'U') IS NOT NULL DROP TABLE [dbo].[blood_banks];
IF OBJECT_ID('dbo.notifications', 'U') IS NOT NULL DROP TABLE [dbo].[notifications];
IF OBJECT_ID('dbo.personal_access_tokens', 'U') IS NOT NULL DROP TABLE [dbo].[personal_access_tokens];
IF OBJECT_ID('dbo.password_reset_tokens', 'U') IS NOT NULL DROP TABLE [dbo].[password_reset_tokens];
IF OBJECT_ID('dbo.users', 'U') IS NOT NULL DROP TABLE [dbo].[users];
IF OBJECT_ID('dbo.sessions', 'U') IS NOT NULL DROP TABLE [dbo].[sessions];
IF OBJECT_ID('dbo.failed_jobs', 'U') IS NOT NULL DROP TABLE [dbo].[failed_jobs];
IF OBJECT_ID('dbo.job_batches', 'U') IS NOT NULL DROP TABLE [dbo].[job_batches];
IF OBJECT_ID('dbo.jobs', 'U') IS NOT NULL DROP TABLE [dbo].[jobs];
IF OBJECT_ID('dbo.cache_locks', 'U') IS NOT NULL DROP TABLE [dbo].[cache_locks];
IF OBJECT_ID('dbo.cache', 'U') IS NOT NULL DROP TABLE [dbo].[cache];
IF OBJECT_ID('dbo.migrations', 'U') IS NOT NULL DROP TABLE [dbo].[migrations];
GO

-- =============================================
-- CREATE TABLES
-- =============================================

-- migrations
CREATE TABLE [dbo].[migrations] (
    [id] INT IDENTITY(1,1) NOT NULL,
    [migration] NVARCHAR(255) NOT NULL,
    [batch] INT NOT NULL,
    CONSTRAINT [PK_migrations] PRIMARY KEY CLUSTERED ([id])
);
GO

-- users
CREATE TABLE [dbo].[users] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [name] NVARCHAR(255) NOT NULL,
    [email] NVARCHAR(255) NOT NULL,
    [email_verified_at] DATETIME NULL,
    [password] NVARCHAR(255) NOT NULL,
    [role] NVARCHAR(255) NOT NULL DEFAULT N'monitoring_user',
    [remember_token] NVARCHAR(100) NULL,
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_users] PRIMARY KEY CLUSTERED ([id])
);
GO

CREATE UNIQUE NONCLUSTERED INDEX [users_email_unique] ON [dbo].[users] ([email]);
GO

-- password_reset_tokens
CREATE TABLE [dbo].[password_reset_tokens] (
    [email] NVARCHAR(255) NOT NULL,
    [token] NVARCHAR(255) NOT NULL,
    [created_at] DATETIME NULL,
    CONSTRAINT [PK_password_reset_tokens] PRIMARY KEY CLUSTERED ([email])
);
GO

-- sessions
CREATE TABLE [dbo].[sessions] (
    [id] NVARCHAR(255) NOT NULL,
    [user_id] BIGINT NULL,
    [ip_address] NVARCHAR(45) NULL,
    [user_agent] NVARCHAR(MAX) NULL,
    [payload] NVARCHAR(MAX) NOT NULL,
    [last_activity] INT NOT NULL,
    CONSTRAINT [PK_sessions] PRIMARY KEY CLUSTERED ([id])
);
GO

CREATE NONCLUSTERED INDEX [sessions_user_id_index] ON [dbo].[sessions] ([user_id]);
CREATE NONCLUSTERED INDEX [sessions_last_activity_index] ON [dbo].[sessions] ([last_activity]);
GO

-- cache
CREATE TABLE [dbo].[cache] (
    [key] NVARCHAR(255) NOT NULL,
    [value] NVARCHAR(MAX) NOT NULL,
    [expiration] INT NOT NULL,
    CONSTRAINT [PK_cache] PRIMARY KEY CLUSTERED ([key])
);
GO

-- cache_locks
CREATE TABLE [dbo].[cache_locks] (
    [key] NVARCHAR(255) NOT NULL,
    [owner] NVARCHAR(255) NOT NULL,
    [expiration] INT NOT NULL,
    CONSTRAINT [PK_cache_locks] PRIMARY KEY CLUSTERED ([key])
);
GO

-- jobs
CREATE TABLE [dbo].[jobs] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [queue] NVARCHAR(255) NOT NULL,
    [payload] NVARCHAR(MAX) NOT NULL,
    [attempts] TINYINT NOT NULL,
    [reserved_at] INT NULL,
    [available_at] INT NOT NULL,
    [created_at] INT NOT NULL,
    CONSTRAINT [PK_jobs] PRIMARY KEY CLUSTERED ([id])
);
GO

CREATE NONCLUSTERED INDEX [jobs_queue_index] ON [dbo].[jobs] ([queue]);
GO

-- job_batches
CREATE TABLE [dbo].[job_batches] (
    [id] NVARCHAR(255) NOT NULL,
    [name] NVARCHAR(255) NOT NULL,
    [total_jobs] INT NOT NULL,
    [pending_jobs] INT NOT NULL,
    [failed_jobs] INT NOT NULL,
    [failed_job_ids] NVARCHAR(MAX) NOT NULL,
    [options] NVARCHAR(MAX) NULL,
    [cancelled_at] INT NULL,
    [created_at] INT NOT NULL,
    [finished_at] INT NULL,
    CONSTRAINT [PK_job_batches] PRIMARY KEY CLUSTERED ([id])
);
GO

-- failed_jobs
CREATE TABLE [dbo].[failed_jobs] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [uuid] NVARCHAR(255) NOT NULL,
    [connection] NVARCHAR(MAX) NOT NULL,
    [queue] NVARCHAR(MAX) NOT NULL,
    [payload] NVARCHAR(MAX) NOT NULL,
    [exception] NVARCHAR(MAX) NOT NULL,
    [failed_at] DATETIME NOT NULL DEFAULT (GETDATE()),
    CONSTRAINT [PK_failed_jobs] PRIMARY KEY CLUSTERED ([id])
);
GO

CREATE UNIQUE NONCLUSTERED INDEX [failed_jobs_uuid_unique] ON [dbo].[failed_jobs] ([uuid]);
GO

-- personal_access_tokens (Laravel Sanctum)
CREATE TABLE [dbo].[personal_access_tokens] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [tokenable_type] NVARCHAR(255) NOT NULL,
    [tokenable_id] BIGINT NOT NULL,
    [name] NVARCHAR(MAX) NOT NULL,
    [token] NVARCHAR(64) NOT NULL,
    [abilities] NVARCHAR(MAX) NULL,
    [last_used_at] DATETIME NULL,
    [expires_at] DATETIME NULL,
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_personal_access_tokens] PRIMARY KEY CLUSTERED ([id])
);
GO

CREATE UNIQUE NONCLUSTERED INDEX [personal_access_tokens_token_unique] ON [dbo].[personal_access_tokens] ([token]);
CREATE NONCLUSTERED INDEX [personal_access_tokens_tokenable_type_tokenable_id_index] ON [dbo].[personal_access_tokens] ([tokenable_type], [tokenable_id]);
CREATE NONCLUSTERED INDEX [personal_access_tokens_expires_at_index] ON [dbo].[personal_access_tokens] ([expires_at]);
GO

-- notifications
CREATE TABLE [dbo].[notifications] (
    [id] UNIQUEIDENTIFIER NOT NULL,
    [type] NVARCHAR(255) NOT NULL,
    [notifiable_type] NVARCHAR(255) NOT NULL,
    [notifiable_id] BIGINT NOT NULL,
    [data] NVARCHAR(MAX) NOT NULL,
    [read_at] DATETIME NULL,
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_notifications] PRIMARY KEY CLUSTERED ([id])
);
GO

CREATE NONCLUSTERED INDEX [notifications_notifiable_type_notifiable_id_index] ON [dbo].[notifications] ([notifiable_type], [notifiable_id]);
GO

-- blood_banks
CREATE TABLE [dbo].[blood_banks] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [name] NVARCHAR(255) NOT NULL,
    [location] NVARCHAR(255) NOT NULL,
    [contact_number] NVARCHAR(255) NOT NULL,
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_blood_banks] PRIMARY KEY CLUSTERED ([id])
);
GO

-- refrigerators
CREATE TABLE [dbo].[refrigerators] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [blood_bank_id] BIGINT NOT NULL,
    [refrigerator_code] NVARCHAR(255) NOT NULL,
    [location] NVARCHAR(255) NOT NULL,
    [is_active] BIT NOT NULL DEFAULT (1),
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_refrigerators] PRIMARY KEY CLUSTERED ([id]),
    CONSTRAINT [refrigerators_blood_bank_id_foreign] FOREIGN KEY ([blood_bank_id]) REFERENCES [dbo].[blood_banks] ([id])
);
GO

CREATE UNIQUE NONCLUSTERED INDEX [refrigerators_refrigerator_code_unique] ON [dbo].[refrigerators] ([refrigerator_code]);
CREATE NONCLUSTERED INDEX [refrigerators_blood_bank_id_is_active_index] ON [dbo].[refrigerators] ([blood_bank_id], [is_active]);
GO

-- blood_bags
CREATE TABLE [dbo].[blood_bags] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [refrigerator_id] BIGINT NOT NULL,
    [bag_number] NVARCHAR(255) NOT NULL,
    [blood_group] NVARCHAR(255) NOT NULL,
    [donor_name] NVARCHAR(255) NOT NULL,
    [collection_date] DATE NOT NULL,
    [expiry_date] DATE NOT NULL,
    [quantity] INT NOT NULL,
    [status] NVARCHAR(255) NOT NULL DEFAULT N'available',
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_blood_bags] PRIMARY KEY CLUSTERED ([id]),
    CONSTRAINT [blood_bags_refrigerator_id_foreign] FOREIGN KEY ([refrigerator_id]) REFERENCES [dbo].[refrigerators] ([id])
);
GO

CREATE UNIQUE NONCLUSTERED INDEX [blood_bags_bag_number_unique] ON [dbo].[blood_bags] ([bag_number]);
CREATE NONCLUSTERED INDEX [blood_bags_blood_group_status_index] ON [dbo].[blood_bags] ([blood_group], [status]);
CREATE NONCLUSTERED INDEX [blood_bags_expiry_date_status_index] ON [dbo].[blood_bags] ([expiry_date], [status]);
GO

-- temperature_logs
CREATE TABLE [dbo].[temperature_logs] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [refrigerator_id] BIGINT NOT NULL,
    [temperature] DECIMAL(5,2) NOT NULL,
    [recorded_at] DATETIME NOT NULL,
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_temperature_logs] PRIMARY KEY CLUSTERED ([id]),
    CONSTRAINT [temperature_logs_refrigerator_id_foreign] FOREIGN KEY ([refrigerator_id]) REFERENCES [dbo].[refrigerators] ([id])
);
GO

CREATE NONCLUSTERED INDEX [temperature_logs_refrigerator_id_recorded_at_index] ON [dbo].[temperature_logs] ([refrigerator_id], [recorded_at]);
CREATE NONCLUSTERED INDEX [temperature_logs_temperature_recorded_at_index] ON [dbo].[temperature_logs] ([temperature], [recorded_at]);
GO

-- alerts
CREATE TABLE [dbo].[alerts] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [refrigerator_id] BIGINT NOT NULL,
    [type] NVARCHAR(255) NOT NULL,
    [message] NVARCHAR(MAX) NOT NULL,
    [triggered_at] DATETIME NOT NULL,
    [is_resolved] BIT NOT NULL DEFAULT (0),
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_alerts] PRIMARY KEY CLUSTERED ([id]),
    CONSTRAINT [alerts_refrigerator_id_foreign] FOREIGN KEY ([refrigerator_id]) REFERENCES [dbo].[refrigerators] ([id])
);
GO

CREATE NONCLUSTERED INDEX [alerts_refrigerator_id_is_resolved_index] ON [dbo].[alerts] ([refrigerator_id], [is_resolved]);
GO

-- blood_bank_user (pivot table)
CREATE TABLE [dbo].[blood_bank_user] (
    [id] BIGINT IDENTITY(1,1) NOT NULL,
    [user_id] BIGINT NOT NULL,
    [blood_bank_id] BIGINT NOT NULL,
    [created_at] DATETIME NULL,
    [updated_at] DATETIME NULL,
    CONSTRAINT [PK_blood_bank_user] PRIMARY KEY CLUSTERED ([id]),
    CONSTRAINT [blood_bank_user_user_id_foreign] FOREIGN KEY ([user_id]) REFERENCES [dbo].[users] ([id]),
    CONSTRAINT [blood_bank_user_blood_bank_id_foreign] FOREIGN KEY ([blood_bank_id]) REFERENCES [dbo].[blood_banks] ([id])
);
GO

CREATE UNIQUE NONCLUSTERED INDEX [blood_bank_user_user_id_blood_bank_id_unique] ON [dbo].[blood_bank_user] ([user_id], [blood_bank_id]);
GO

-- =============================================
-- INSERT SEED DATA
-- =============================================

-- migrations
SET IDENTITY_INSERT [dbo].[migrations] ON;
INSERT INTO [dbo].[migrations] ([id], [migration], [batch]) VALUES
    (1, N'0001_01_01_000000_create_users_table', 1),
    (2, N'0001_01_01_000001_create_cache_table', 1),
    (3, N'0001_01_01_000002_create_jobs_table', 1),
    (4, N'2026_05_25_130656_create_blood_banks_table', 1),
    (5, N'2026_05_25_130657_create_refrigerators_table', 1),
    (6, N'2026_05_25_130658_create_blood_bags_table', 1),
    (7, N'2026_05_25_130659_create_temperature_logs_table', 1),
    (8, N'2026_05_25_130700_create_alerts_table', 1),
    (9, N'2026_05_25_130701_create_blood_bank_user_table', 1),
    (10, N'2026_05_25_132520_create_personal_access_tokens_table', 1),
    (11, N'2026_05_25_151000_create_notifications_table', 1);
SET IDENTITY_INSERT [dbo].[migrations] OFF;
GO

-- users (passwords are bcrypt hashes)
SET IDENTITY_INSERT [dbo].[users] ON;
INSERT INTO [dbo].[users] ([id], [name], [email], [email_verified_at], [password], [role], [remember_token], [created_at], [updated_at]) VALUES
    (1, N'Admin User', N'admin@test.com', NULL, N'$2y$12$3L9dEedYGAPv2fmnCT0HaeyZWJveEIFgFLPIwpiNEdnZKExyx1HH.', N'admin', NULL, '2026-05-25 15:33:08.577', '2026-05-25 15:33:08.577'),
    (2, N'Staff User', N'staff@test.com', NULL, N'$2y$12$6D.hBkjqWCgZC69Eg7nw1.kyH1yxz7H1kX1YpVxnoYtfjIN1cMa4G', N'blood_bank_staff', NULL, '2026-05-25 15:33:08.760', '2026-05-25 15:33:08.760'),
    (3, N'Monitor User', N'monitor@test.com', NULL, N'$2y$12$TN59NoMEEUIZyypJFpeEG.700JzyvBK2PwymgRSN3NamPYZa9qufm', N'monitoring_user', NULL, '2026-05-25 15:33:08.943', '2026-05-25 15:33:08.943');
SET IDENTITY_INSERT [dbo].[users] OFF;
GO

-- blood_banks
SET IDENTITY_INSERT [dbo].[blood_banks] ON;
INSERT INTO [dbo].[blood_banks] ([id], [name], [location], [contact_number], [created_at], [updated_at]) VALUES
    (1, N'Central Blood Bank', N'Downtown', N'1234567890', '2026-05-25 15:33:08.950', '2026-05-25 15:33:08.950'),
    (2, N'City General Blood Bank', N'123 Main Street, Downtown', N'555-0100', '2026-05-25 15:35:20.237', '2026-05-25 15:35:20.237');
SET IDENTITY_INSERT [dbo].[blood_banks] OFF;
GO

-- refrigerators
SET IDENTITY_INSERT [dbo].[refrigerators] ON;
INSERT INTO [dbo].[refrigerators] ([id], [blood_bank_id], [refrigerator_code], [location], [is_active], [created_at], [updated_at]) VALUES
    (1, 1, N'REF-001', N'Storage Room A', 1, '2026-05-25 15:33:08.980', '2026-05-25 15:33:08.980'),
    (2, 1, N'REF-002', N'Storage Room B', 1, '2026-05-25 15:33:08.987', '2026-05-25 15:33:08.987');
SET IDENTITY_INSERT [dbo].[refrigerators] OFF;
GO

-- blood_bags
SET IDENTITY_INSERT [dbo].[blood_bags] ON;
INSERT INTO [dbo].[blood_bags] ([id], [refrigerator_id], [bag_number], [blood_group], [donor_name], [collection_date], [expiry_date], [quantity], [status], [created_at], [updated_at]) VALUES
    (1, 1, N'BAG-001', N'A+', N'John Doe', '2026-05-20', '2026-06-29', 450, N'available', '2026-05-25 15:33:08.987', '2026-05-25 15:33:08.987'),
    (2, 1, N'BAG-002', N'O-', N'Jane Smith', '2026-05-15', '2026-05-26', 350, N'available', '2026-05-25 15:33:08.993', '2026-05-25 15:33:08.993'),
    (3, 2, N'BAG-003', N'B+', N'Mike Johnson', '2026-04-15', '2026-05-24', 400, N'available', '2026-05-25 15:33:08.993', '2026-05-25 15:33:08.993'),
    (4, 1, N'BAG-001A', N'O+', N'Michael Scott', '2026-05-25', '2026-06-29', 450, N'available', '2026-05-25 15:36:15.393', '2026-05-25 15:36:15.393'),
    (5, 1, N'BAG-002A', N'O+', N'Michael Scott', '2026-05-25', '2026-06-29', 450, N'available', '2026-05-25 15:45:21.677', '2026-05-25 15:45:21.677');
SET IDENTITY_INSERT [dbo].[blood_bags] OFF;
GO

-- blood_bank_user (pivot)
SET IDENTITY_INSERT [dbo].[blood_bank_user] ON;
INSERT INTO [dbo].[blood_bank_user] ([id], [user_id], [blood_bank_id], [created_at], [updated_at]) VALUES
    (1, 1, 1, '2026-05-25 15:33:08.967', '2026-05-25 15:33:08.967'),
    (2, 2, 1, '2026-05-25 15:33:08.967', '2026-05-25 15:33:08.967'),
    (3, 3, 1, '2026-05-25 15:33:08.967', '2026-05-25 15:33:08.967');
SET IDENTITY_INSERT [dbo].[blood_bank_user] OFF;
GO

-- temperature_logs
SET IDENTITY_INSERT [dbo].[temperature_logs] ON;
INSERT INTO [dbo].[temperature_logs] ([id], [refrigerator_id], [temperature], [recorded_at], [created_at], [updated_at]) VALUES
    (1, 1, 5.90, '2026-05-25 15:19:08.997', '2026-05-25 15:33:08.997', '2026-05-25 15:33:08.997'),
    (2, 1, 2.70, '2026-05-25 15:20:09.000', '2026-05-25 15:33:09.000', '2026-05-25 15:33:09.000'),
    (3, 1, 3.50, '2026-05-25 15:21:09.003', '2026-05-25 15:33:09.003', '2026-05-25 15:33:09.003'),
    (4, 1, 5.30, '2026-05-25 15:22:09.003', '2026-05-25 15:33:09.003', '2026-05-25 15:33:09.003'),
    (5, 1, 5.50, '2026-05-25 15:23:09.007', '2026-05-25 15:33:09.007', '2026-05-25 15:33:09.007'),
    (6, 1, 4.20, '2026-05-25 15:24:09.007', '2026-05-25 15:33:09.007', '2026-05-25 15:33:09.007'),
    (7, 1, 4.40, '2026-05-25 15:25:09.010', '2026-05-25 15:33:09.010', '2026-05-25 15:33:09.010'),
    (8, 1, 3.30, '2026-05-25 15:26:09.010', '2026-05-25 15:33:09.010', '2026-05-25 15:33:09.010'),
    (9, 1, 5.60, '2026-05-25 15:27:09.013', '2026-05-25 15:33:09.013', '2026-05-25 15:33:09.013'),
    (10, 1, 5.60, '2026-05-25 15:28:09.013', '2026-05-25 15:33:09.013', '2026-05-25 15:33:09.013'),
    (11, 1, 2.30, '2026-05-25 15:29:09.017', '2026-05-25 15:33:09.017', '2026-05-25 15:33:09.017'),
    (12, 1, 3.80, '2026-05-25 15:30:09.020', '2026-05-25 15:33:09.020', '2026-05-25 15:33:09.020'),
    (13, 1, 4.40, '2026-05-25 15:31:09.023', '2026-05-25 15:33:09.023', '2026-05-25 15:33:09.023'),
    (14, 1, 3.30, '2026-05-25 15:32:09.023', '2026-05-25 15:33:09.023', '2026-05-25 15:33:09.023'),
    (15, 1, 5.30, '2026-05-25 15:33:09.027', '2026-05-25 15:33:09.027', '2026-05-25 15:33:09.027'),
    (16, 1, 8.50, '2026-05-25 14:30:00.000', '2026-05-25 15:38:13.803', '2026-05-25 15:38:13.803'),
    (17, 1, 8.30, '2026-05-25 14:30:00.000', '2026-05-25 15:38:38.137', '2026-05-25 15:38:38.137'),
    (18, 1, 8.60, '2026-05-25 14:30:00.000', '2026-05-25 15:38:46.777', '2026-05-25 15:38:46.777');
SET IDENTITY_INSERT [dbo].[temperature_logs] OFF;
GO

-- =============================================
-- NOTE: The following tables have no data and
-- are left empty (created by schema above):
--   alerts, cache, cache_locks, failed_jobs,
--   job_batches, jobs, notifications,
--   password_reset_tokens, personal_access_tokens,
--   sessions
-- =============================================

PRINT 'Blood Inventory System database restored successfully.';
GO
