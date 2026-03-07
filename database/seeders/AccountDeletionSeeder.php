<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Page;

class AccountDeletionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ensure Account Deletion page exists and is published
        $page = Page::updateOrCreate(
            ['slug' => 'account-deletion'],
            [
                'title' => 'Account Deletion and Data Removal',
                'author_id' => 1,
                'status' => 'publish',
                'content' => '<h1>Account Deletion and Data Removal</h1>
<p>In accordance with our commitment to user privacy and data protection, <strong>VIRTUARD</strong> provides a clear and transparent process for account deletion and personal data removal.</p>

<h2>1. Automated Deletion via Profile Settings</h2>
<p>Registered users may initiate an automated deletion of their account and all associated data through the following procedure:</p>
<ul>
    <li>Log in to your account at <strong>www.virtuard.com</strong>.</li>
    <li>Navigate to the <strong>Profile Settings</strong> (Impostazioni Profilo) section.</li>
    <li>Locate and click the <strong>Delete Account</strong> button at the bottom of the page.</li>
    <li>Confirm your request. Please be advised that this action is <strong>permanent and irreversible</strong>.</li>
</ul>

<h2>2. Manual Deletion Request</h2>
<p>If you are unable to access the automated tools or wish to exercise your right to be forgotten via official channels, you may contact our data protection team:</p>
<ul>
    <li><strong>Email:</strong> info@virtuard.com</li>
    <li><strong>Subject:</strong> Official Account Deletion Request</li>
    <li><strong>Required Details:</strong> Registered email address and Username.</li>
</ul>

<h2>3. Data Retention and Effects of Deletion</h2>
<p>Upon processing your request, all personal profiles, saved 3D/360 virtual tours, and associated metadata will be permanently removed from our active databases within <strong>48 hours</strong>. Please note that certain information may be retained in encrypted backups for a limited period to comply with legal, tax, or regulatory obligations.</p>

<p>For any further inquiries regarding your data, please contact us at <a href="mailto:info@virtuard.com">info@virtuard.com</a>.</p>'
            ]
        );

        $translations = [
            'it' => [
                'title' => 'Eliminazione Account e Rimozione Dati',
                'content' => '<h1>Eliminazione Account e Rimozione Dati</h1>
<p>In conformità con il nostro impegno per la privacy degli utenti e la protezione dei dati, <strong>VIRTUARD</strong> fornisce un processo chiaro e trasparente per l\'eliminazione dell\'account e la rimozione dei dati personali.</p>
<h2>1. Eliminazione Automatica tramite Impostazioni Profilo</h2>
<p>Gli utenti registrati possono avviare l\'eliminazione automatica del proprio account e di tutti i dati associati seguendo questa procedura:</p>
<ul>
    <li>Accedi al tuo account su <strong>www.virtuard.com</strong>.</li>
    <li>Vai alla sezione <strong>Impostazioni Profilo</strong>.</li>
    <li>Trova e clicca sul pulsante <strong>Cancella Account</strong> in fondo alla pagina.</li>
    <li>Conferma la tua richiesta. Ti informiamo che questa azione è <strong>permanente e irreversibile</strong>.</li>
</ul>
<h2>2. Richiesta di Eliminazione Manuale</h2>
<p>Se non riesci ad accedere agli strumenti automatici o desideri esercitare il tuo diritto all\'oblio tramite i canali ufficiali, puoi contattare il nostro team di protezione dati:</p>
<ul>
    <li><strong>Email:</strong> info@virtuard.com</li>
    <li><strong>Oggetto:</strong> Richiesta Ufficiale di Eliminazione Account</li>
    <li><strong>Dettagli Richiesti:</strong> Indirizzo email registrato e Nome Utente.</li>
</ul>'
            ],
            'es' => [
                'title' => 'Eliminación de Cuenta y Eliminación de Datos',
                'content' => '<h1>Eliminación de Cuenta y Eliminación de Datos</h1>
<p>De acuerdo con nuestro compromiso con la privacidad del usuario y la protección de datos, <strong>VIRTUARD</strong> proporciona un proceso claro y transparente para la eliminación de la cuenta y la eliminación de datos personales.</p>
<h2>1. Eliminación automatizada a través de la configuración del perfil</h2>
<p>Los usuarios registrados pueden iniciar una eliminación automatizada de su cuenta y todos los datos asociados a través del siguiente procedimiento:</p>
<ul>
    <li>Inicie sesión en su cuenta en <strong>www.virtuard.com</strong>.</li>
    <li>Vaya a la sección <strong>Configuración del perfil</strong>.</li>
    <li>Busque y haga clic en el botón <strong>Eliminar cuenta</strong> en la parte inferior de la página.</li>
    <li>Confirma tu solicitud. Tenga en cuenta que esta acción es <strong>permanente e irreversible</strong>.</li>
</ul>
<h2>2. Solicitud de eliminación manual</h2>
<p>Si no puede acceder a las herramientas automatizadas o desea ejercer su derecho al olvido a través de canales oficiales, puede ponerse en contacto con nuestro equipo de protección de datos:</p>
<ul>
    <li><strong>Email:</strong> info@virtuard.com</li>
    <li><strong>Asunto:</strong> Solicitud oficial de eliminación de cuenta</li>
    <li><strong>Detalles requeridos:</strong> Dirección de correo electrónico registrada y nombre de usuario.</li>
</ul>'
            ],
            'id' => [
                'title' => 'Penghapusan Akun dan Penghapusan Data',
                'content' => '<h1>Penghapusan Akun dan Penghapusan Data</h1>
<p>Sesuai dengan komitmen kami terhadap privasi pengguna dan perlindungan data, <strong>VIRTUARD</strong> menyediakan proses yang jelas dan transparan untuk penghapusan akun dan penghapusan data pribadi.</p>
<h2>1. Penghapusan Otomatis melalui Pengaturan Profil</h2>
<p>Pengguna terdaftar dapat memulai penghapusan otomatis akun mereka dan semua data terkait melalui prosedur berikut:</p>
<ul>
    <li>Masuk ke akun Anda di <strong>www.virtuard.com</strong>.</li>
    <li>Buka bagian <strong>Pengaturan Profil</strong> (Profile Settings).</li>
    <li>Temukan dan klik tombol <strong>Hapus Akun</strong> di bagian bawah halaman.</li>
    <li>Konfirmasikan permintaan Anda. Harap diperhatikan bahwa tindakan ini bersifat <strong>permanen dan tidak dapat dibatalkan</strong>.</li>
</ul>
<h2>2. Permintaan Penghapusan Manual</h2>
<p>Jika Anda tidak dapat mengakses alat otomatis atau ingin menggunakan hak Anda untuk dilupakan melalui saluran resmi, Anda dapat menghubungi tim perlindungan data kami:</p>
<ul>
    <li><strong>Email:</strong> info@virtuard.com</li>
    <li><strong>Subjek:</strong> Permintaan Penghapusan Akun Resmi</li>
    <li><strong>Detail yang Diperlukan:</strong> Alamat email terdaftar dan Nama Pengguna.</li>
</ul>'
            ],
            'ko' => [
                'title' => '계정 삭제 및 데이터 제거',
                'content' => '<h1>계정 삭제 및 데이터 제거</h1>
<p>사용자 개인 정보 보호 및 데이터 보호에 대한 당사의 약속에 따라 <strong>VIRTUARD</strong>는 계정 삭제 및 개인 데이터 제거를 위한 명확하고 투명한 프로세스를 제공합니다.</p>
<h2>1. 프로필 설정을 통한 자동 삭제</h2>
<p>등록된 사용자는 다음 절차를 통해 계정 및 모든 관련 데이터의 자동 삭제를 시작할 수 있습니다.</p>
<ul>
    <li><strong>www.virtuard.com</strong>에서 계정에 로그인합니다.</li>
    <li><strong>프로필 설정</strong>(Profile Settings) 섹션으로 이동합니다.</li>
    <li>페이지 하단의 <strong>계정 삭제</strong> 버튼을 찾아 클릭합니다.</li>
    <li>요청을 확인합니다. 이 작업은 <strong>영구적이며 되돌릴 수 없음</strong>을 유의하십시오.</li>
</ul>
<h2>2. 수동 삭제 요청</h2>
<p>자동화된 도구에 액세스할 수 없거나 공식 채널을 통해 잊혀질 권리를 행사하려는 경우 당사의 데이터 보호 팀에 문의할 수 있습니다.</p>
<ul>
    <li><strong>이메일:</strong> info@virtuard.com</li>
    <li><strong>제목:</strong> 공식 계정 삭제 요청</li>
    <li><strong>필수 세부 정보:</strong> 등록된 이메일 주소 및 사용자 이름.</li>
</ul>'
            ],
            'ru' => [
                'title' => 'Удаление аккаунта и удаление данных',
                'content' => '<h1>Удаление аккаунта и удаление данных</h1>
<p>В соответствии с нашими обязательствами по обеспечению конфиденциальности пользователей и защите данных, <strong>VIRTUARD</strong> обеспечивает четкий и прозрачный процесс удаления учетной записи и удаления личных данных.</p>
<h2>1. Автоматическое удаление через настройки профиля</h2>
<p>Зарегистрированные пользователи могут инициировать автоматическое удаление своей учетной записи и всех связанных данных с помощью следующей процедуры:</p>
<ul>
    <li>Войдите в свою учетную запись на сайте <strong>www.virtuard.com</strong>.</li>
    <li>Перейдите в раздел <strong>Настройки профиля</strong> (Profile Settings).</li>
    <li>Найдите и нажмите кнопку <strong>Удалить аккаунт</strong> в нижней части страницы.</li>
    <li>Подтвердите свой запрос. Обратите внимание, что это действие является <strong>постоянным и необратимым</strong>.</li>
</ul>
<h2>2. Запрос на удаление вручную</h2>
<p>Если вы не можете получить доступ к автоматизированным инструментам или хотите реализовать свое право на забвение через официальные каналы, вы можете связаться с нашей группой по защите данных:</p>
<ul>
    <li><strong>Email:</strong> info@virtuard.com</li>
    <li><strong>Тема:</strong> Официальный запрос на удаление аккаунта</li>
    <li><strong>Необходимые данные:</strong> Зарегистрированный адрес электронной почты и имя пользователя.</li>
</ul>'
            ],
            'zh' => [
                'title' => '注销账号和删除数据',
                'content' => '<h1>注销账号和删除数据</h1>
<p>根据我们对用户隐私和数据保护的承诺，<strong>VIRTUARD</strong> 为注销账号和删除个人数据提供了清晰透明的流程。</p>
<h2>1. 通过个人资料设置自动注销</h2>
<p>注册用户可以通过以下程序启动自动注销其账号及所有相关数据：</p>
<ul>
    <li>登录您的账号 <strong>www.virtuard.com</strong>。</li>
    <li>导航到<strong>个人资料设置</strong> (Profile Settings) 部分。</li>
    <li>找到并点击页面底部的<strong>注销账号</strong>按钮。</li>
    <li>确认您的请求。请注意，此操作是<strong>永久且不可撤销的</strong>。</li>
</ul>
<h2>2. 手动注销请求</h2>
<p>如果您无法访问自动化工具或希望通过官方渠道行使被遗忘权，您可以联系我们的数据保护团队：</p>
<ul>
    <li><strong>电子邮件：</strong> info@virtuard.com</li>
    <li><strong>主题：</strong> 正式注销账号请求</li>
    <li><strong>所需详细信息：</strong> 注册邮箱地址和用户名。</li>
</ul>'
            ],
        ];

        foreach ($translations as $locale => $data) {
            $page->translateOrNew($locale)->fill($data);
        }
        $page->save();

        // 2. Ensure Privacy Policy is published (Google Play 404 fix)
        $privacyPolicy = Page::where('slug', 'privacy-policy')->first();
        if ($privacyPolicy) {
            $privacyPolicy->status = 'publish';
            $privacyPolicy->save();
        } else {
            // Create a default one if it doesn't exist for some reason
            Page::create([
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'author_id' => 1,
                'status' => 'publish',
                'content' => '<h1>Privacy Policy</h1><p>Our privacy policy describes how we collect, use, and handle your information when you use our services...</p>'
            ]);
        }

        // 3. Enable the "Permanently Delete" setting for the user profile
        setting_update_item('user_enable_permanently_delete', '1');
    }
}
