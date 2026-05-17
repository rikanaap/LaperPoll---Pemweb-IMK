<div class="{{ $index >= 10 ? 'hidden md:block' : '' }} flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
    <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
        <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
    </div>
    <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">{{ $bahan->nama }}</p>
</div>